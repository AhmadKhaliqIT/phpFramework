<?php
namespace Core\DataTables;
use Core\Collection\Collection;
use Core\Database\Builder;
use Core\DataTables\Utilities\Request;
use Core\Support\Arr;
use Core\Support\Contract\Arrayable;
use Core\Support\Str;
use Exception;

class QueryDataTable extends DataTableAbstract
{
    public Builder $builder;
    //public array $columns;

    public function __construct(Builder $builder)
    {
        $this->request = new Request();
        $this->builder = $builder;
        /*foreach ($this->request->columns as $column)
        {
            $this->columns[]=$column['data'];
        }*/
        $sample_row  = $this->builder->cloneWithout([])->first();
        $this->columns = array_keys((array)$sample_row);
    }

    public static function canCreate($source): bool
    {
        return  $source instanceof Builder;
    }

    /*public static function create($source)
    {
        return parent::create($source);
    }*/

    /**
     * Resolve callback parameter instance.
     *
     * @return $this
     */
    protected function resolveCallbackParameter()
    {
        return $this;
    }

    protected function defaultOrdering()
    {
        foreach ($this->request->order as $orderCOl)
        {
            $this->builder->orderBy(
                $this->request->columns[$orderCOl['column']]['data'],
                $orderCOl['dir']
            );

        }
    }

    protected function globalSearch($keyword)
    {
        //echo $keyword;
        foreach ($this->request->columns as $col)
        {
            if($col['searchable']=='true')
            {
                $this->builder->orWhere($col['data'],'LIKE','%'.$keyword.'%');
            }

        }
    }


    public function make($mDataSupport = true): string
    {

        if (is_array($this->request->order))
        {
            $this->defaultOrdering();
        }

        if (isset($this->request->search['value']) and strlen($this->request->search['value'])>1 )
        {
            $this->globalSearch($this->request->search['value']);
        }

        $this->paging();
        return '
        {
            "draw":'.($this->request->draw??2).',
            "iTotalRecords":'.$this->totalCount().',
            "iTotalDisplayRecords":'.$this->totalCount().',
            "data":'.$this->builder_data().'
        }
        ';
    }

    public function builder_data(): bool|string
    {
        $DB_DATA = $this->builder->get();

        foreach ($DB_DATA as $id => $row)
        {


            foreach($this->columnDef['edit'] as $edit)
            {
                $ColName = $edit['name'];
                if (is_callable($edit['content']))
                    $DB_DATA[$id]->$ColName = $edit['content']($row);
            }

            foreach($this->columnDef['append'] as $append)
            {
                $ColName = $append['name'];
                if (is_callable($append['content']))
                    $DB_DATA[$id]->$ColName = $append['content']($row);
            }


            $cols = (array_keys((array)$row));

            foreach($cols as $col)
            {
                if(!isset($this->columnDef['raw']) or !is_array($this->columnDef['raw']) or !in_array($col,$this->columnDef['raw']))
                {
                    $DB_DATA[$id]->$col = htmlspecialchars($DB_DATA[$id]->$col , ENT_QUOTES);
                }
            }



        }


        //die();



        return json_encode($DB_DATA);
    }

    /**
     * Count total items.
     *
     * @return int
     * @throws Exception
     */
    public function totalCount(): int
    {
        return (int) $this->builder->count();
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

                $cmp = strnatcasecmp($first[$column], $second[$column]);

                if ($cmp != 0) {
                    return $cmp;
                }
            }

            // all elements were equal
            return 0;
        };

        return $sorter;
    }


    public function paging()
    {
        $limit = (int) $this->request->input('length') ?? 10;
        if($limit!=-1)
            $this->builder->skip($this->request->input('start'))->take($limit);
    }



    /**
     * Perform column search.
     *
     * @return void
     */
    public function columnSearch() /*TODO iN VAGHEAN LAZEME?*/
    {

    }


}