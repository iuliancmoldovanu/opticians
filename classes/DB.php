<?php

class DB{
    private static $_instance = null;
    private $_pdo,
        $_query,
        $_error = FALSE,
        $_results,
        $_count = 0;

    private function __construct(){
        /* Connect to an ODBC database using driver invocation */
        $dsn = 'mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db');
        $username = Config::get('mysql/username');
        $password = Config::get('mysql/password');

        try {
            $this->_pdo = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    public function query($sql, $params = array()){
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if(count($params)){
                foreach ($params as $param){
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            if($this->_query->execute()){
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            }else{
                $this->_error = true;
            }
        }
        return $this;
    }

    public function action($action, $table_name, $where= array(), $orderBy = ''){
        if(count($where) === 3){
            $operators = array('=', '>', '<', '<=', '>=', 'LIKE');

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            if(in_array($operator, $operators)){
                $sql = "{$action} FROM {$table_name} WHERE {$field} {$operator} ? {$orderBy}";
                if(!$this->query($sql, array($value))->error()){
                    return $this;
                }
            }
        }
        if(count($where) === 0){
            $sql = "{$action} FROM {$table_name}";
            if(!$this->query($sql, array(''))->error()){
                return $this;
            }
        }
        return false;
    }

    public function getRows($table_name){
        return $this->action('SELECT *', $table_name);
    }

    public function get($table_name, $where, $orderBy = ''){
        return $this->action('SELECT *', $table_name, $where, $orderBy);
    }

    public function insert($table_name, $array_values){
        if(count($array_values)) {
            $keys = array_keys($array_values);
            $values = '';
            $x = 1;

            foreach ($array_values as $value) {
                $values .= '?';
                if ($x < count($array_values)) {
                    $values .= ', ';
                }
                $x++;
            }

            $sql = "INSERT INTO {$table_name}
                (`" . implode('`, `', $keys) . "`)
                VALUES
                ({$values})
            ";
            if(!$this->query($sql, $array_values)->error()){
                return true;
            }
        }
        return false;
    }

    public function update($table_name, $array_values, $field_name, $field_value){
        if(count($array_values)) {
            $values = '';
            $x = 1;

            foreach ($array_values as $value => $key) {
                $values .= "{$value} = ?";
                if ($x < count($array_values)) {
                    $values .= ', ';
                }
                $x++;
            }

            $sql = "UPDATE {$table_name}
                SET {$values}
                WHERE {$field_name} = {$field_value}
            ";
            if(!$this->query($sql, $array_values)->error()){
                return true;
            }
        }
        return false;
    }

    public function delete($table_name, $where){
        return $this->action('DELETE', $table_name, $where);
    }

    public function result(){
        return $this->_results;
    }

    public function first(){
        return $this->result()[0];
    }

    public function error(){
        return $this->_error;
    }

    public function count(){
        return $this->_count;
    }
}

?>