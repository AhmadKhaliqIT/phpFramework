<?php
namespace Core\DataTables;
use Core\Collection\Collection;
use Core\DataTables\DataTableAbstract;
use Core\DataTables\Utilities\Request;
use Core\Support\Arr;
use Core\Support\Contract\Arrayable;
use Core\Support\Str;

class CollectionDataTable extends DataTableAbstract{

    /**
     * Collection object.
     *
     * @var \Illuminate\Support\Collection
     */
    public $collection;


    /**
     * Collection object.
     *
     * @var \Illuminate\Support\Collection
     */
    public $original;

    /**
     * CollectionEngine constructor.
     *
     * @param \Illuminate\Support\Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->request    = new Request();
//        $this->config     = app('datatables.config');
        $this->collection = $collection;
        $this->original   = $collection;
//        $this->columns    = array_keys($this->serialize($collection->first()));
    }


    /**
     * Can the DataTable engine be created with these parameters.
     *
     * @param mixed $source
     * @return bool
     */
    public static function canCreate($source)
    {
        return is_array($source)  || $source instanceof Collection;
    }

    /**
     * Factory method, create and return an instance for the DataTable engine.
     *
     * @param array|\Illuminate\Support\Collection $source
     * @return CollectionDataTable|DataTableAbstract
     */
    public static function create($source)
    {
        if (is_array($source)) {
            $source = new Collection($source);
        }
        return parent::create($source);

    }


    /**
     * Resolve callback parameter instance.
     *
     * @return $this
     */
    protected function resolveCallbackParameter()
    {
        return $this;
    }


    /**
     * Perform default query orderBy clause.
     */
    protected function defaultOrdering()
    {


        $criteria = $this->request->orderableColumns();
        if (! empty($criteria)) {
            $sorter = $this->getSorter($criteria);


            $this->collection = $this->collection
                ->map(function ($data) {
                    return Arr::dot($data);
                })
                ->sort($sorter)
                ->map(function ($data) {
                    foreach ($data as $key => $value) {
                        unset($data[$key]);
                        Arr::set($data, $key, $value);
                    }

                    return $data;
                });
        }
    }



    /**
     * Perform global search for the given keyword.
     *
     * @param string $keyword
     */
    protected function globalSearch($keyword)
    {
        $keyword = Str::lower($keyword);


        $this->collection = $this->collection->filter(function ($row) use ($keyword) {
            $this->isFilterApplied = true;

            $data = $this->serialize($row);
            foreach ($this->request->searchableColumnIndex() as $index) {
                $column = $this->getColumnName($index);
                $value = Arr::get($data, $column);
                if (! $value || is_array($value)) {
                    if (! is_numeric($value)) {
                        continue;
                    }

                    $value = (string) $value;
                }

                $value = Str::lower($value);
                if (Str::contains($value, $keyword)) {
                    return true;
                }
            }

            return false;
        });
    }


    /**
     * Organizes works.
     *
     * @param bool $mDataSupport
     * @return \Illuminate\Http\JsonResponse
     */
    public function make($mDataSupport = true)
    {
//        try {
            $this->totalRecords = $this->totalCount();

            if ($this->totalRecords) {
                $results   = $this->results();
                $processed = $this->processResults($results, $mDataSupport);
                $output    = $this->transform($results, $processed);

                $this->collection = collect($output);
                $this->ordering();
                $this->filterRecords();
                $this->paginate();

                $this->revertIndexColumn($mDataSupport);
            }

            return $this->render($this->collection->values()->all());
//        } catch (\Exception $exception) {
//           dd($exception->);
//        }
    }



    /**
     * Count total items.
     *
     * @return int
     */
    public function totalCount()
    {
        return $this->totalRecords ? $this->totalRecords : $this->collection->count();
    }


    /**
     * Get results.
     *
     * @return mixed
     */
    public function results()
    {
        return $this->collection->all();
    }


    /**
     * Get array sorter closure.
     *
     * @param array $criteria
     * @return \Closure
     */
    protected function getSorter(array $criteria)
    {
        $sorter = function ($a, $b) use ($criteria) {
            foreach ($criteria as $orderable) {
                $column    = $this->getColumnName($orderable['column']);
                $direction = $orderable['direction'];
                if ($direction === 'desc') {
                    $first  = $b;
                    $second = $a;
                } else {
                    $first  = $a;
                    $second = $b;
                }

//                if ($this->config->isCaseInsensitive()) {
                    $cmp = strnatcasecmp($first[$column], $second[$column]);
//                } else {
//                    $cmp = strnatcmp($first[$column], $second[$column]);
//                }
                if ($cmp != 0) {
                    return $cmp;
                }
            }

            // all elements were equal
            return 0;
        };

        return $sorter;
    }


    /**
     * Perform column search.
     *
     * @return void
     */
    public function columnSearch()
    {

       // $columns = $this->request->get('columns', []);
        $columns = $this->request->columns;
        for ($i = 0, $c = count($columns); $i < $c; $i++) {
            if ($this->request->isColumnSearchable($i)) {
                $this->isFilterApplied = true;

                $regex   = $this->request->isRegex($i);
                $column  = $this->getColumnName($i);
                $keyword = $this->request->columnKeyword($i);

                $this->collection = $this->collection->filter(
                    function ($row) use ($column, $keyword, $regex) {
                        $data = $this->serialize($row);

                        $value = Arr::get($data, $column);

                        if ($this->config->isCaseInsensitive()) {
                            if ($regex) {
                                return preg_match('/' . $keyword . '/i', $value) == 1;
                            }

                            return strpos(Str::lower($value), Str::lower($keyword)) !== false;
                        }

                        if ($regex) {
                            return preg_match('/' . $keyword . '/', $value) == 1;
                        }

                        return strpos($value, $keyword) !== false;
                    }
                );
            }
        }
    }


    /**
     * Serialize collection.
     *
     * @param  mixed $collection
     * @return mixed|null
     */
    protected function serialize($collection)
    {
        return $collection instanceof Arrayable ? $collection->toArray() : (array) $collection;
    }


    /**
     * Revert transformed DT_RowIndex back to it's original values.
     *
     * @param bool $mDataSupport
     */
    private function revertIndexColumn($mDataSupport)
    {
        if ($this->columnDef['index']) {
            $index = $mDataSupport ? config('datatables.index_column', 'DT_RowIndex') : 0;
            $start = (int) $this->request->input('start');
            $this->collection->transform(function ($data) use ($index, &$start) {
                $data[$index] = ++$start;

                return $data;
            });
        }
    }


    /**
     * Count results.
     *
     * @return int
     */
    public function count()
    {
        return $this->collection->count() > $this->totalRecords ? $this->totalRecords : $this->collection->count();
    }

}