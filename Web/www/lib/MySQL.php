<?php
/*
**************************************************************************************
* Maker : 정영훈(m4700q@nate.com)														 *
* Create Date : 2010.1.18															 *
* Update Date : 2010.1.26															 *
* Create Purpose : DB Query 처리를 위한 클래스											 *
* Program Version : 2.0																 *
* Program Name : Soyou DB 클래스													 	 *
* Update 내용 :	기존의 사용방식을 단순화 시킴										 		 *
**************************************************************************************
*/
class MySQL { 
	var $_resource = null;
	var $isdev;
	
	public function MySQL(){
		$this->_resource = NULL;
		$this->isdev = 1;
		$this->connect();
	}
	
	//DB Connection 
	public function connect() {
		global $DB_CONN;
		if (!$DB_CONN) $DB_CONN = get_db_conn();
			$this->_resource = $DB_CONN;
			//$this->query("set names utf8");
	}

	public function parse($param) {
		$result = array('field'=>'*', 'where'=>null, 'group'=>null, 'order'=>null, 'limit'=>null);
		if (!is_array($param)) return $result;
		if (array_key_exists('field', $param) && $param['field']) $result['field'] = $param['field'];
		if (array_key_exists('where', $param) && $param['where']) $result['where'] = 'WHERE ' . $param['where'];
		if (array_key_exists('group', $param) && $param['group']) $result['group'] = 'GROUP BY ' . $param['group'];
		if (array_key_exists('order', $param) && $param['order']) $result['order'] = 'ORDER BY ' . $param['order'];
		if (array_key_exists('limit', $param) && $param['limit']) $result['limit'] = 'LIMIT ' . $param['limit'];
		return $result;
	}
	
	public function fetch($resrc=null) {
		if (!$resrc) return false;
		return mysql_fetch_assoc($resrc);
		//return mysql_fetch_array($resrc);
	}

	public function querystring($args=null,$chk=false) {
		if (is_array($args)) {
			$data = array();
			foreach ($args as $k => $v) {

				if (preg_match('/(now\(\))/i', $v) && !$chk) {
					$data[] = "`$k`={$v}";
				} else if(preg_match('/([\+\-\*\/])=[0-9]+/', $v) && !$chk) {
					$data[] = "`$k`={$v}";
				} else {
					//$v = str_replace(array("&", "<?"), array("&amp;", "&lt?;"), addslashes(stripslashes($v)));
					$v = str_replace(array("<?"), array( "&lt?;"), addslashes(stripslashes($v)));
					$data[] = "`$k`='{$v}'";
				}

			}
			return implode(', ', $data);
		}
		return $args;
	}

    // Update Query 처리
	public function update($table, $field, $where=null){
		$field = $this->querystring($field);
		if ($where) $where = 'WHERE ' . $where;
		$query = trim("UPDATE " . $table . " SET {$field} {$where}");
		return $this->query($query);
	}
    // Insert Query 처리 
    public function insert($table, $field){ 
		$field = $this->querystring($field);
		$query = trim("INSERT INTO " . $table . " SET {$field}");
		return $this->query($query);
    }

	// 한즐 Insert Query 처리 
    public function insertLine($table, $field){ 
		$field = $this->querystring($field);
		$query = trim("INSERT INTO " . $table . " {$field}");
		return $this->query($query);
    }

	// 무조건 쉼표처리 Insert Query 처리  (백업복구때문에 만듬) 2011-11-07 by.cjy
    public function insertOnlystring($table, $field){ 
		$field = $this->querystring($field,true);
		$query = trim("INSERT INTO " . $table . " SET {$field}");
		return $this->query($query);
    }

    // Select Query 처리
    public function select($table, $param=array()){ 
		extract($this->parse($param));
		$query = trim("SELECT {$field} FROM " . $table . " {$where} {$group} {$order} {$limit}");
		$resrc = $this->query($query);
		if (!$resrc) return array();
		return $this->fetch_all($resrc);
   }
	public function fetch_all($stmt) {
		if(!$stmt) return array();
		$rowset = array();
		while($row = $this->fetch($stmt)) {
			$rowset[] = $row;
			//echo($row[name]);
		}
		return $rowset;
	 }

	public function row($table, $param=array()) {
		extract($this->parse($param));
		$sql = trim("SELECT {$field} FROM " . $table . " {$where} {$group} {$order} {$limit}");
		return $this->fetch($this->query($sql));
	}

	// Delete Query 처리
    public function delete($table, $where=null){ 
		if($where) $where = 'WHERE ' . $where;
		$query = trim("DELETE FROM " . $table . " {$where}");
		return $this->query($query);
    }

    // 쿼리를 받아서 최종 처리
    public function query($query){ 
        //$res =  @mysql_query($query,$this->resource);
		$res = @mysql_query($query, $this->_resource) or exit('db query error' . (isdev() ? "[".mysql_errno()."] : {$query}<br><b>".mysql_error()."</b>" : ''));
		if (!$res) return false;
		$type = strtolower(substr($query, 0, 6));
		if ($type == 'select') return (mysql_num_rows($res)) ? $res : false;
		if ($type == 'update') return intval(mysql_affected_rows($this->_resource));
		if ($type == 'delete') return intval(mysql_affected_rows($this->_resource));
		if ($type == 'insert') {
			$insert_id = mysql_insert_id($this->_resource);
			return $insert_id ? $insert_id : intval(mysql_affected_rows($this->_resource));
		}
		return $res;
    }

	
	//하나의 값만 불러옴
	public function one($table, $field, $where=null) {
		if ($where) $where = 'WHERE ' . $where;
		$sql = trim("SELECT {$field} FROM " . $table . " {$where}");
		if ($resrc = $this->query($sql)) return mysql_result($resrc, 0);
		return false;
	}

	//COUNT 값 
	public function num($table, $field, $where=null, $group=null) {
		$field = 'COUNT(' . $field . ')';
		if ($where) $where = 'WHERE ' . $where;
		if ($group) $group	=	'group by '. $group;
		$sql = trim("SELECT {$field} FROM " . $table . " {$where}"." {$group}");
		//echo $sql;
		if ($resrc = $this->query($sql)) return mysql_result($resrc, 0);
		return false;
	}

	//레코드삭제
	public function rDel($table, $where){
		$where = 'where '.$where;
		$sql = trim("delete from ". $table." {$where} ");
		$this->query($sql);
		return false;
	}

	//MAX 값
	public function max($table, $field, $where=null) {
		if ($where) $where = 'WHERE ' . $where;
		$sql = trim("SELECT MAX(`{$field}`) FROM " . $table . " {$where}");
		if ($resrc = $this->query($sql)) return mysql_result($resrc, 0);
		return false;
	}

	//MIN 값
	public function min($table, $field, $where=null) {
		if ($where) $where = 'WHERE ' . $where;
		$sql = trim("SELECT MIN(`{$field}`) FROM " . $table . " {$where}");
		if ($resrc = $this->query($sql)) return mysql_result($resrc, 0);
		return false;
	}

	//optimize
	public function optimize($table) {
		$sql = trim("OPTIMIZE TABLE " . $table);
		$this->query($sql);
		return false;
	}



}