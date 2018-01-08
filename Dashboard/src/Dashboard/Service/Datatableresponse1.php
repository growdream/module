<?php

namespace Dashboard\Service;

use Doctrine\ORM\Query\Expr\Join;

/*
 * this is for data table
 * get parameter from action and fetch the data from table 
 * return data in json encode
 * created by Navin
 * 
 * can use agrigate functions  
 */

class Datatableresponse1 {

    protected $entitymanager;
    protected $joinAlias;
    protected $uId;
    protected $checkAccess;

    public function __construct($em) {
        $this->entitymanager = $em;
    }

    public function setJoinAlias($alias) {
        $this->joinAlias = $alias;
    }

    public function getJoinAlias() {
        return $this->joinAlias;
    }

    public function setLoginUser($uId) {
        $this->uId = $uId;
    }

    public function getLoginUser() {
        return $this->uId;
    }

    public function setCheckAccess($access = 0) {
        return $this->checkAccess = $access;
    }

    public function getCheckAccess() {
        return $this->checkAccess;
    }

    /**
     * Create the data output array for the DataTables rows
     *
     *  @param  array $columns Column information array
     *  @param  array $data    Data from the SQL get
     *  @return array          Formatted data in a row based format
     */
    static function data_output($columns, $data) {
        $out = array();

        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = array();

            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];

                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
//                 $datakey=  isset($columns[$j]['as'])?$columns[$j]['as']:$columns[$j]['db'];
                    $datakey = isset($columns[$j]['as']) ? $columns[$j]['as'] : substr($columns[$j]['db'], strpos($columns[$j]['db'], ".") + 1);
                    $row[$column['dt']] = $data[$i][$datakey];
                }
            }

            $out[] = $row;
        }

        return $out;
    }

    static function data_output1($data) {
        $out = array();
//        $column= array("0","1","2","3","4","5","6","7");
        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = array();

            $jen = 8; //columns
            for ($j = 0; $j < $jen; $j++) {
                $row[] = $data[$i][$j];
            }

            $out[] = $row;
        }

        return $out;
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit($request, $columns) {
        $limit = [];

        if (isset($request['start']) && $request['length'] != -1) {
            $limit[] = intval($request['start']);
            $limit[] = intval($request['length']);
        }

        return $limit;
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL order by clause
     */
    static function order($request, $columns) {
        $order = '';

        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();
            $dtColumns = self::pluck($columns, 'dt');

            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                            'ASC' :
                            'DESC';

                    $orderBy[] = ((isset($column['order']) && "" != $column['order']) ?
                                    $column['order'] :
                                    (isset($column['as']) ?
                                            $column['as'] :
                                            $column['db']));
                    $orderBy[] = $dir;
                }
            }

            $order = $orderBy;
        }

        return $order;
    }

    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     *  @return string SQL where clause
     */
    static function filter($request, $columns, &$bindings, $and = null) {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = self::pluck($columns, 'dt');

        if (isset($request['search']) && isset($request['search']['value']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];

            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['searchable'] == 'true') {
                    $binding = self::bind($bindings, '%' . $str . '%', \PDO::PARAM_STR);
                    //$tbalise=(isset($column['alise']))?$column['alise'].".":'';
                    $globalSearch[] = $column['db'] . " LIKE " . "'%$str%'";
                }
            }
        }

        // Individual column filtering
        for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
            $requestColumn = $request['columns'][$i];
            $columnIdx = array_search($requestColumn['data'], $dtColumns);
            $column = $columns[$columnIdx];

            $str = $requestColumn['search']['value'];

            if ($requestColumn['searchable'] == 'true' &&
                    $str != '') {
                $binding = self::bind($bindings, '%' . $str . '%', \PDO::PARAM_STR);
                //$tbalise=(isset($column['alise']))?$column['alise'].".":'';
                $columnSearch[] = $column['db'] . " LIKE " . "'%$str%'"; //.$binding;
            }
        }

        // Combine the filters into a single string
        $where = '';

        if (count($globalSearch)) {
            $where = implode(' OR ', $globalSearch);
        }

        if (count($columnSearch)) {
            $where = $where === '' ?
                    implode(' AND ', $columnSearch) :
                    $where . ' AND ' . implode(' AND ', $columnSearch);
        }

//        if ($where !== '') {
//            $where = $where;
//        }
        $where = $and ? ("(" . $and . ")" . ($where ? " and (" . $where . ")" : "")) : ($where ? $where : "");

        return $where;
    }

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array|PDO $conn PDO connection resource or connection parameters array
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @return array          Server-side processing response array
     */
    public function simple($request, $table, $columns) {
        $bindings = array();
        // Build the SQL query string from the request
        $limit = self::limit($request, $columns);
        $order = self::order($request, $columns);
        $where = self::filter($request, $columns, $bindings);
        // Main query to actually get the data
        $em = $this->entitymanager;

        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select(implode(",", self::pluckfield($columns, 'db')));
        $queryBuilder->from($table[0]['tb'], $table[0]['alise']);
        for ($i = 1; $i < count($table); $i++) {
            $queryBuilder->innerJoin($table[$i]['tb'], $table[$i]['alise'], 'WITH', $table[$i]['on']);
        }

        $queryBuilder->add("where", $where)
                ->orderBy($order[0], $order[1])
                ->setFirstResult($limit[0])
                ->setMaxResults($limit[1]);
        $result = $queryBuilder->getQuery()->getArrayResult();

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($queryBuilder);
        $paginator->setUseOutputWalkers(false);

        $recordsFiltered = count($paginator);
        $recordsTotal = count($paginator);


        /*         * Output * */
        return array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => self::data_output($columns, $result),
        );
    }

    /**
     * The difference between this method and the `simple` one, is that you can
     * apply additional `where` conditions to the SQL queries and join methods. These can be in
     * one of two forms:
     *
     *  @param  array "$request" Data sent to server by DataTables
     * 
     *  @param  string "$table" SQL table to query
     * tb       : table name*
     * alise    : table alise*
     * on       : condition to join table
     * join     : type to join table [inner,left]
     * 
     * 
     *  @param  array "$columns" Column information array
     * db       : column name with alise*
     * dt       : array index to get result*
     * as       : field name alise
     * order    : order by field name 
     * 
     *  @param  string or Array "$and" WHERE condition to apply to the result set
     * groupby  :   field name to group by
     * 0 index  :   where condition
     * 
     *  @return array          Server-side processing response array
     */
    public function complex($request, $table, $columns, $and = null) {
        $bindings = array();

        $and0 = is_array($and) ? $and[0] : $and; // if array take 0 indexed string else consider itself as where string
        // Build the SQL query string from the request
        $limit = self::limit($request, $columns);
        $order = self::order($request, $columns);
        $where = self::filter($request, $columns, $bindings, $and0);
        $groupby = (is_array($and) && isset($and['groupby'])) ? $and['groupby'] : "";
        // Main query to actually get the data

        $em = $this->entitymanager;

        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select(implode(",", self::pluckfield($columns, 'db')));
        $queryBuilder->from($table[0]['tb'], $table[0]['alise']);
        for ($i = 1; $i < count($table); $i++) {
            if (!isset($table[$i]['join']) || $table[$i]['join'] == 'inner') {
                $queryBuilder->innerJoin($table[$i]['tb'], $table[$i]['alise'], 'WITH', $table[$i]['on']);
            } else if ($table[$i]['join'] == 'left') {
                $queryBuilder->leftJoin($table[$i]['tb'], $table[$i]['alise'], 'WITH', $table[$i]['on']);
            }

//           else if($table[$i]['join']=='right')
// {$queryBuilder->rightJoin($table[$i]['tb'], $table[$i]['alise'], 'WITH', $table[$i]['on']);}
        }

        $queryBuilder->add("where", $where);
        if (!empty($groupby)) {
            $queryBuilder->groupBy($groupby);
        }

        $queryBuilder->orderBy($order[0], $order[1])
                ->setFirstResult($limit[0])
                ->setMaxResults($limit[1]);
//        $qry=$queryBuilder->getQuery()->getSQL();//DQL(); 
//        echo $qry;die;
        if ($this->getCheckAccess()) {
            $queryBuilder = $this->getAccessLevel($queryBuilder);
        }
        $result = $queryBuilder->getQuery()->getArrayResult();

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($queryBuilder);
        $paginator->setUseOutputWalkers(false);

        $recordsFiltered = count($paginator);
        $recordsTotal = count($paginator);


        /*         * Output * */
        return array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => self::data_output($columns, $result),
            "count" => count($result),
//            "qry"=>$qry,
//            "order"=>$order,
        );
    }

    public function getAccessLevel($qb) {
        $joinAlias = $this->getJoinAlias();
        $uId = $this->getLoginUser();
        $em = $this->entitymanager;
        $where = $qb->getDQLParts()['where'];
        $repository = $em->getRepository("\Owner\Entity\AccessFirstLevel");
        $accessLevel = $repository->findOneBy(array("uId" => $uId));
        if ($accessLevel) {
            $level = $accessLevel->levelId;
        } else {
            $level = "";
        }
        switch ($level) {
            case "2":
                $qb->innerJoin('\Owner\Entity\Signup', 'level_user', Join::WITH, 'level_user.uId=' . $joinAlias . ".uId");
                $qb->innerJoin('\Owner\Entity\AccessSecondLevel', 'level_asl', Join::WITH, 'level_asl.refId=level_user.orgId');
                ($where == "") ? $qb->where("level_asl.uId=" . $uId) : $qb->andWhere("level_asl.uId=" . $uId);
                break;
            case "3":
                $qb->innerJoin('\Owner\Entity\EmpJoiningentity', 'level_eje', Join::WITH, 'level_eje.uId=' . $joinAlias . ".uId");
                $qb->innerJoin('\Owner\Entity\AccessSecondLevel', 'level_asl', Join::WITH, 'level_asl.refId=level_eje.br_id');
                ($where == "") ? $qb->where("level_asl.uId=" . $uId) : $qb->andWhere("level_asl.uId=" . $uId);
                break;
            case "4":
                $qb->innerJoin('\Owner\Entity\EmpJoiningentity', 'level_eje', Join::WITH, 'level_eje.uId=' . $joinAlias . ".uId");
                $qb->innerJoin('\Owner\Entity\AccessSecondLevel', 'level_asl', Join::WITH, 'level_asl.refId=level_eje.bdr_id');
                ($where == "") ? $qb->where("level_asl.uId=" . $uId) : $qb->andWhere("level_asl.uId=" . $uId);
                break;
        }
        return $qb;
    }

    public function complex2($request, $result1) {
        $recordsFiltered = count($result1);
        $recordsTotal = $recordsFiltered;


        /*         * Output * */
        return array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => self::data_output1($result1),
//            "count"=>count($result),
//            "qry"=>$qry,
//            "order"=>$order,
        );
    }

    /*     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */

    static function bind(&$a, $val, $type) {
        $key = ':binding_' . count($a);

        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => $type
        );

        return $key;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array, 
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values
     */
    static function pluck($a, $prop) {
        $out = array();

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }

        return $out;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array, 
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @return array        Array of property values with alise
     */
    static function pluckfield($a, $prop) {
        $out = array();

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $as = '';
            if (isset($a[$i]['as'])) {
                $as = ' as ' . $a[$i]['as'];
            }

            //$tbalise=(isset($a[$i]['alise']))?$a[$i]['alise'] . '.' :'';
            $out[] = $a[$i][$prop] . $as;
        }

        return $out;
    }

}
