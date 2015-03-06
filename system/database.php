<?php
namespace system;

class database {

    protected $db_conn_id;

    static $hostname = "localhost";
    static $username = "root";
    static $password = "root";
    static $database = "zhinengtou";
    static $port = "3306";
    var $bind_marker    = '?';

    private $result_id;

    public function __construct() {
        $this->db_conn_id = $this->connect();
        $this->_set_chatset("UTF8","");
        $this->_execute("SET NAMES 'utf8'");
    }


    private function connect() {
        return mysqli_connect(self::$hostname, self::$username, self::$password, self::$database, self::$port);
    }

    private function _set_db($db) {

    }

    private function _execute($sql) {
        $result = @mysqli_query($this->db_conn_id, $this->compile_binds($sql,FALSE));
        return $result;
    }

    private function affected_rows() {
        return @mysqli_affected_rows($this->db_conn_id);
    }

    public function insert_id() {
        return @mysqli_insert_id($this->db_conn_id);
    }

    private function _set_chatset($chatset,$collation) {
        return @mysqli_set_charset($this->db_conn_id, $charset);
    }

    public function query($sql) {
        return $this->result_id = $this->_execute($sql);
    }

    public function fetch_array() {
        if ( $this->result_id === FALSE ) {
            return FALSE;
        }

        mysqli_data_seek($this->result_id,0);

        $result_array = array();
        while($row = mysqli_fetch_assoc($this->result_id)) {
           $result_array[] = $row; 
        }

        return $result_array;
    }

    public function insert($table,$keys,$vals) {
        foreach ( $vals as $key=>$val ) {
            $vals[$key] = $this->escape($val);
        }
        return $this->_execute("INSERT INTO {$table}(".implode(",",$keys).") VALUES(".implode(", ",$vals).")");
    }

    public function update($table, $values, $where, $orderby = array(), $limit = FALSE)
    {             
        foreach ($values as $key => $val)
        {         
            $valstr[] = $key." = ".$val;
        }         

        $limit = ( ! $limit ) ? '' : ' LIMIT '.$limit;

        $orderby = (count($orderby) >= 1)?' ORDER BY '.implode(", ", $orderby):'';

        $sql = "UPDATE ".$table." SET ".implode(', ', $valstr);

        $sql .= ($where != '' AND count($where) >=1) ? " WHERE ".implode(" ", $where) : ''; 

        $sql .= $orderby.$limit;

        return $sql; 
    }             

    public function delete($table, $where = array(), $like = array(), $limit = FALSE)
    {         
        $conditions = '';

        if (count($where) > 0 OR count($like) > 0)
        {     
            $conditions = "\nWHERE ";
            $conditions .= implode("\n", $this->ar_where);

            if (count($where) > 0 && count($like) > 0)
            { 
                $conditions .= " AND ";
            } 
            $conditions .= implode("\n", $like);
        }     

        $limit = ( ! $limit ) ? '' : ' LIMIT '.$limit;

        return "DELETE FROM ".$table.$conditions.$limit; 
    }

    public function error_message() {          
        return mysqli_error($this->db_conn_id);
    }          

    public function getOne($sql) {
        
    }

    public function getRow($sql) {
        
    }

    public function close() {
        @mysql_close($this->db_conn_id);
    }

	function compile_binds($sql, $binds)
	{
		if (strpos($sql, $this->bind_marker) === FALSE)
		{
			return $sql;
		}

		if ( ! is_array($binds))
		{
			$binds = array($binds);
		}

		$segments = explode($this->bind_marker, $sql);

		if (count($binds) >= count($segments)) {
			$binds = array_slice($binds, 0, count($segments)-1);
		}

		$result = $segments[0];
		$i = 0;
		foreach ($binds as $bind)
		{
			$result .= $this->escape($bind);
			$result .= $segments[++$i];
		}

		return $result;
	}
	function escape($str)
	{
		if (is_string($str))
		{
			$str = "'".$this->escape_str($str)."'";
		}
		elseif (is_bool($str))
		{
			$str = ($str === FALSE) ? 0 : 1;
		}
		elseif (is_null($str))
		{
			$str = 'NULL';
		}

		return $str;
	}
	function escape_str($str, $like = FALSE)
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
	   		{
				$str[$key] = $this->escape_str($val, $like);
	   		}

	   		return $str;
	   	}

		if (function_exists('mysqli_real_escape_string') AND is_resource($this->db_conn_id))
		{
			$str = mysqli_real_escape_string($str, $this->db_conn_id);
            echo $str;
		}
		else
		{
			$str = addslashes($str);
		}

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			$str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
		}

		return $str;
	}
}

