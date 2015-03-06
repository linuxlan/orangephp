<?php
namespace system;

if ( MODULE_ILLUMINATE ) {
    class models extends  \Illuminate\Database\Eloquent\Model {

        public $timestamps = false;

    }
}else {
    class models {

        public $db;

        public function __construct() {
            $this->db = &$GLOBALS['db'];
        }

        protected function sqls($where, $font = ' AND ') {
            if (is_array($where)) {
                $sql = '';  
                foreach ($where as $key=>$val) {
                    if ( is_numeric($key)  ) { 
                        $sql .= $sql ? " $font $val " : " $val ";
                    }          
                    else {   
                        $sql .= $sql ? " $font `$key` = '$val' " : " `$key` = '$val'";
                    }          

                }              
                return $sql;

            } else {           
                return $where;
            }                  
        }                      

        public function update($table,$where,$data) {
            if (is_array($where)) $where = $this->sqls($where);
            $where = $where == '' ? '' : " WHERE {$where}";
            if (is_array($data)) $data = $this->sqls($data,",");
            $data = $data == '' ? '' : " {$data}";
            $sql = "UPDATE ".$table." SET {$data}  {$where}";
            return $this->db->query($sql);
        }
    }


}
