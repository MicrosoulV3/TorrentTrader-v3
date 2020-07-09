<?php


function &SQL_Query ($query) {
	return new SQL_Query ($query);
}

function &SQL_Query_exec ($query) {
	$sql = new SQL_Query ($query);
	return $sql->execute();
}

class SQL_Query {
	private $query = "";
	private $params = array();

	function &__construct ($query) {
		$this->query = $query;
		return $this;
	}

	function &p ($param) {
		if (is_numeric($param)) {
			$this->params[] = $param;
		} elseif (is_array($param)) {
			$this->params[] = implode(", ", array_map(array(&$this, "escape"), $param));
		} else {
			$this->params[] = $this->escape($param);
		}
		return $this;
	}

	function &p_name ($param) {
		$this->params[] = "`".mysqli_real_escape_string($GLOBALS["DBconnector"],$param)."`";
		return $this;
	}

	function escape ($s) {
		if (is_numeric($s))
			return $s;
		return "'".mysqli_real_escape_string($GLOBALS["DBconnector"],$s)."'";
	}

	function read () {
		$ret = "";
		if (count($this->params)) {
			reset($this->params);
			for ($i = 0; $i < strlen($this->query); $i++) {
				if ($this->query[$i] == "?") {
					list(, $val) = thisEach($this->params);
					$ret .= $val;
				} else {
					$ret .= $this->query[$i];
				}
			}
			reset($this->params);
		} else {
			$ret = $this->query;
		}
		return $ret;
	}

	function &execute () {
		$query = $this->read();
		$res = mysqli_query($GLOBALS["DBconnector"],$query);

		if ($res || mysqli_errno($GLOBALS["DBconnector"]) == 1062) {
			return $res;
		}
		$mysqli_error = mysqli_error($GLOBALS["DBconnector"]);
		$mysqli_errno = mysqli_errno($GLOBALS["DBconnector"]);

		// If debug_backtrace() is available, we can find exactly where the query was called from
		if (function_exists("debug_backtrace")) {
			$bt = debug_backtrace();

			$i = 1;

			if ($bt[$i]["function"] == "SQL_Query_exec_cached" || $bt[$i]["function"] == "get_row_count_cached" || $bt[$i]["function"] == "get_row_count")
				$i++;

			$line = $bt[$i]["line"];
			$file = str_replace(getcwd().DIRECTORY_SEPARATOR, "", $bt[$i]["file"]);
			$msg = "Database Error in $file on line $line: $mysqli_error. Query was: $query.";
		} else {
			$file = str_replace(getcwd().DIRECTORY_SEPARATOR, "", $_SERVER["SCRIPT_FILENAME"]);
			$msg = "Database Error in $file: $mysqli_error. Query was: $query";
		}
        
        mysqli_query($GLOBALS["DBconnector"],"INSERT INTO `sqlerr` (`txt`, `time`) 
                     VALUES (".sqlesc($msg).", '".get_date_time()."')");

        if ( function_exists('show_error_msg') )
		     show_error_msg("Database Error", "Database Error. Please report this to an administrator.", 1);
	}
}

/* Example Usage:

// Note: Any values passed to p() or p_name() MUST NOT be escaped, this will be done internally.
// for p() arrays are also taken and imploded with , for use in IN(...)
// p_name() is for field/table names
// p() is for where conditions, insert/update values, etc...

$ids = range(1, 10);
//$res = SQL_Query("SELECT `id`, `username` FROM `users` WHERE ? IN (?) ORDER BY ? ASC")->p_name("id")->p($ids)->p_name("id")->execute();

$q = SQL_Query("SELECT `id`, `username` FROM `users` WHERE ? IN (?) ORDER BY ? ASC")->p_name("id")->p($ids)->p_name("id");

echo "Query: ".$q->read()."\n";
$res = $q->execute();

while ($row = mysqli_fetch_array($res)) {
	echo "$row[id] - $row[username]\n";
}

// Trigger a SQL error to test logging
SQL_Query("SELECT")->execute();
*/
?>