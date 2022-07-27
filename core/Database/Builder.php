<?php
/* بسم الله الرحمن الرحیم */
/**
 * phpFramework
 *
 * @author     Ahmad Khaliq
 * @author     Mojtaba Zadegi
 * @copyright  2022 Ahmad Khaliq
 * @license    https://github.com/AhmadKhaliqIT/phpFramework/blob/main/LICENSE
 * @link       https://github.com/AhmadKhaliqIT/phpFramework/
 */



namespace Core\Database;

use Exception;

class Builder {
    private string $_table;
    private string $_query;
    public  array $_select=[];
    private array $_where=[];
    private array $_insert_update_values=[];
    private string $_limit='';
    private string $_offset='';
    private string $_groupBy='';
    private array  $_orderBy=[];
    private $_connection;


    public function __construct() //done
    {
        global $_connection;
        $this->_connection = $_connection;
    }


    public function table ($name): Builder //done
    {
        $this->_table = $name;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function rawQuery ($query): \mysqli_result|bool
    {
        $result = $this->_connection->query($query);
        if(isset($this->_connection->error) and !empty($this->_connection->error))
            throw new Exception('Database Error: '.$this->_connection->error);
        return $result;
    }

    public function select(...$cols): Builder //done
    {
        foreach ($cols as $col)
        {
            if (is_array($col))
                $this->_select = array_merge($this->_select,$col);
            else
                $this->_select[] = $col;
        }
        return $this;
    }

    public function arrayToWhere($array,$type = 'AND') //done
    {
        foreach ($array as $key=>$value)
        {
            $value = '"'.$this->_connection->real_escape_string($value).'"';
            $this->_where[]= [$type,$key,'=',$value];
        }
    }


    public function where($col,$operator=null,$value=null): Builder //done
    {
        if(is_array($col))
        {
            $this->arrayToWhere($col);
            return $this;
        }

        if ($value == null)
        {
            $value = $operator;
            $operator = '=';
        }

        $value = '"'.$this->_connection->real_escape_string($value).'"';

        $this->_where[]= ['AND',$col,$operator,$value];
        return $this;
    }

    public function orWhere($col,$operator=null,$value=null): Builder //done
    {
        if(is_array($col))
        {
            $this->arrayToWhere($col,'OR');
            return $this;
        }

        if ($value == null)
        {
            $value = $operator;
            $operator = '=';
        }

        $value = '"'.$this->_connection->real_escape_string($value).'"';

        $this->_where[]= ['OR',$col,$operator,$value];
        return $this;
    }

    public function whereIn($col,$array): Builder //done
    {
        for ($i=0;$i<count($array);$i++)
        {
            $array[$i] = '"'.$this->_connection->real_escape_string($array[$i]).'"';

        }

        $this->_where[]= ['AND',$col,'IN','('.implode(',',$array).')'];
        return $this;
    }

    public function whereNotIn($col,$array): Builder
    {
        for ($i=0;$i<count($array);$i++)
        {
            $array[$i] = '"'.$this->_connection->real_escape_string($array[$i]).'"';
        }
        $this->_where[]= ['AND',$col,'NOT IN','('.implode(',',$array).')'];
        return $this;
    }

    public function whereId($id): Builder  //done
    {
        self::where('id',$id);
        return $this;
    }

    public function orderBy($col,$sort='ASC'): Builder  //done
    {
        $this->_orderBy[] = ' '.$col.' '.$sort;
        return $this;
    }

    public function groupBy($groupBy): Builder  //done
    {
        $this->_groupBy = ' '.$groupBy.' ';
        return $this;
    }

    /* Alias to set the "limit" value of the query. */
    public function take($value): static
    {
        return $this->limit($value);
    }

    public function limit($limit): Builder //done
    {
        $this->_limit = ' '.$limit.' ';
        return $this;
    }

    /* Alias to set the "offset" value of the query. */
    public function skip($value): static
    {
        return $this->offset($value);
    }

    public function offset($offset): Builder //done
    {
        $this->_offset = ' '.$offset.' ';
        return $this;
    }

    public function value($col): ?string //done
    {
        self::select($col);
        $row = self::first();
        if(isset($row) and isset($row->$col))
            return $row->$col;
        return null;
    }

    public function pluck() {
        /* todo waiting */
    }


    private function prepare_where_clause(): string
    {
        $WHERE_Clause = '';
        if(count($this->_where) > 0)
        {
            $WHERE_Clause = ' WHERE';
            $is_first = true;
            foreach ($this->_where as $where)
            {
                if ($is_first)
                {
                    $where[0] = '';
                    $is_first = false;
                }
                $WHERE_Clause .= ' '.$where[0].' '.$where[1].' '.$where[2].' '.$where[3];
            }
        }
        return $WHERE_Clause;
    }

    private function prepare_query($type)  //done
    {
        if (count($this->_select)<=0)
            $this->_select[] = '*';

        $query = '';
        if ($type == 'SELECT')
            $query = 'SELECT '.implode(',',$this->_select).' FROM '.$this->_table;

        if ($type == 'DELETE')
            $query = 'DELETE FROM '.$this->_table;


        if ($type == 'UPDATE')
        {
            $query = 'UPDATE '.$this->_table .' SET ';
            if(count($this->_insert_update_values)>0)
            {
                $arr = [];
                foreach ($this->_insert_update_values as $row => $value)
                    $arr[] = $row.'="'.$this->_connection->real_escape_string($value).'"';
                $query .= implode(',',$arr);
            }
        }

        if ($type == 'INSERT')
        {
            $query = 'INSERT INTO '.$this->_table .' ';
            if(count($this->_insert_update_values)>0)
            {
                $Cols_arr = [];
                $Values_Arr = [];
                foreach ($this->_insert_update_values as $row =>$value)
                {
                    $Cols_arr[] = $row;
                    $Values_Arr[] = '"'.$this->_connection->real_escape_string($value).'"';
                }

                $query .= ' ('.implode(',',$Cols_arr).')';
                $query .= ' VALUES ';
                $query .= ' ('.implode(',',$Values_Arr).')';
            }

            $this->_query = $query;
            return;
        }


        $query .= $this->prepare_where_clause();

        if($this->_groupBy != '')
            $query .= ' GROUP BY '.$this->_groupBy;


        if(count($this->_orderBy)>0)
            $query .= ' ORDER BY '.implode(',',$this->_orderBy);

        if($this->_limit != '')
            $query .= ' LIMIT '.$this->_limit;

        if($this->_offset != '')
            $query .= ' OFFSET '.$this->_offset;

        $this->_query = $query;
    }

    /**
     * @throws Exception
     */
    public function get(): \Core\Collection\Collection   //done
    {
        $this->prepare_query('SELECT');
        //print_r($this->_query);echo"\n";
        $result = $this->rawQuery($this->_query);
        $output_result=[];
        if ($result->num_rows > 0)
            while($row = $result->fetch_assoc()) {
                $output_result[] = (object) $row;
            }
       return collect($output_result);
    }


    /**
     * @throws Exception
     */
    public function first() {//done
        self::limit(1);
        $first = self::get();
        if (isset($first[0]))
            return $first[0];

        return [];
    }


    /**
     * @throws Exception
     */
    public function delete($id = null): bool //done
    {
        if (! is_null($id)) {
            self::where('id', $id);
        }
        $this->prepare_query('DELETE');
        return $this->rawQuery($this->_query);
    }

    /**
     * @throws Exception
     */
    public function insert(array $values): bool //done
    {
        if (empty($values)) {
            return true;
        }
        if (! is_array($values)) {
            return true;
        }

        $this->_insert_update_values = $values;
        $this->prepare_query('INSERT');
        return $this->rawQuery($this->_query);
    }

    public function LastInsertedId(): int|string //done
    {
        return $this->_connection->insert_id;
    }

    /**
     * @throws Exception
     */
    public function insertGetId(array $values): int|string //done
    {
        $this->insert($values);
        return $this->LastInsertedId();
    }

    public function insertOrIgnore() {
        /* todo */
    }

    /**
     * @throws Exception
     */
    public function update(array $values): bool //done
    {
        if (empty($values)) {
            return true;
        }
        if (! is_array($values)) {
            return true;
        }

        $this->_insert_update_values = $values;
        $this->prepare_query('UPDATE');
        return $this->rawQuery($this->_query);
    }

    /**
     * @throws Exception
     */
    public function updateOrInsert(array $attributes, array $values = []): bool
    {
        if (! $this->cloneWithout(['_where'])->where($attributes)->exists()) {
            return $this->insert(array_merge($attributes, $values));
        }

        return (bool) $this->update($values);
    }

    /**
     * @throws Exception
     */
    public function increment($col, $value=1): bool //done
    {
        if (! is_numeric($value)) {
            throw new Exception('Non-numeric value passed to increment method.');
        }
        return $this->rawQuery('UPDATE '.$this->_table.' SET '.$col.'='.$col.'+1 '.$this->prepare_where_clause().'');
    }

    /**
     * @throws Exception
     */
    public function decrement($col, $value=1): bool //done
    {
        if (! is_numeric($value)) {
            throw new Exception('Non-numeric value passed to increment method.');
        }
        return $this->rawQuery('UPDATE '.$this->_table.' SET '.$col.'='.$col.'-1 '.$this->prepare_where_clause().'');
    }

    public function dump() { //done
        print_r(get_object_vars($this));
    }

    /**
     * @throws Exception
     */
    public function sum($column): int //done
    {
        $res = $this->cloneWithout(['_select'])->select(['SUM('.$column.') as output'])->get();
        if (isset($res[0]) and isset($res[0]->output))
            return $res[0]->output;
        return 0;
    }

    /**
     * @throws Exception
     */
    public function min($column) {//done
        $res = $this->cloneWithout(['_select'])->select(['MIN('.$column.') as output'])->get();
        if (isset($res[0]) and isset($res[0]->output))
            return $res[0]->output;
        return null;
    }

    /**
     * @throws Exception
     */
    public function max($column) {//done
        $res = $this->cloneWithout(['_select'])->select(['MAX('.$column.') as output'])->get();
        if (isset($res[0]) and isset($res[0]->output))
            return $res[0]->output;
        return null;
    }

    /**
     * @throws Exception
     */
    public function avg($column)//done
    {
        $res = $this->cloneWithout(['_select'])->select(['AVG('.$column.') as output'])->get();
        if (isset($res[0]) and isset($res[0]->output))
            return $res[0]->output;
        return null;
    }

    /**
     * @throws Exception
     */
    public function count($columns = '*') {//done
        $res = $this->cloneWithout(['_select','_offset','_limit'])->select(['count('.$columns.') as output'])->get();
        if (isset($res[0]) and isset($res[0]->output))
            return $res[0]->output;
        return null;
    }

    /**
     * @throws Exception
     */
    public function exists(): ?bool //done
    {
        $result = $this->rawQuery('SELECT EXISTS(select * from '.$this->_table.'  '.$this->prepare_where_clause().') as result;');
        $res = $result->fetch_assoc();
        return isset($res['result']) and $res['result']==1;
    }

    /**
     * @throws Exception
     */
    public function doesntExist(): bool //done
    {
        return !$this->exists();
    }

    /**
     * Clone the query without the given properties.
     *
     * @param  array  $properties
     * @return static
     */
    public function cloneWithout(array $properties): static
    {
        return tap(clone $this, function ($clone) use ($properties) {
            foreach ($properties as $property) {
                if (is_array($clone->{$property}))
                    $clone->{$property} = [];
                elseif(is_string($clone->{$property}))
                    $clone->{$property} = '';
                elseif(is_int($clone->{$property}))
                    $clone->{$property} = 0;
                else
                    $clone->{$property} = null;
            }
        });
    }


}
