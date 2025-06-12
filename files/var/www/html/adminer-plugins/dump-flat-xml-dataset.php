<?php

/** Dump to XML format specifically PHPUnit's Flat XML DataSet structure <dataset><TABLE_NAME COLUMN_NAME=VALUE COLUMN_NAME=VALUE /></dataset>
* @link http://www.adminer.org/plugins/#use
* @link http://phpunit.de/manual/3.7/en/database.html#database.available-implementations
* @author Michal Brašna
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerDumpFlatXmlDataSet {
	/** @access protected */
	var $dataset = false;
	
	function dumpFormat() {
		return array('flat-xml-dataset' => 'Flat XML DataSet');
	}

	function dumpTable($table, $style, $is_view = false) {
		if ($_POST["format"] == "flat-xml-dataset") {
			return true;
		}
	}
	
	function _dataset() {
		echo "</dataset>\n";
	}
	
	function dumpData($table, $style, $query) {
		if ($_POST["format"] !== "flat-xml-dataset") {
			return;
		}
		
		if (!$this->dataset) {
			$this->dataset = true;
			echo "<?xml version='1.0' ?>\n";
			echo "<dataset>\n";
			register_shutdown_function(array($this, '_dataset'));
		}
		
		$connection = Adminer\connection();
		$result = $connection->query($query);
		if ($result) {
			while ($row = $result->fetch_assoc()) {
				echo "\t<". Adminer\h($table) ."";
				foreach ($row as $key => $val) {
					echo isset($val)
						? " " . Adminer\h($key) . "='" . Adminer\h($val) . "'"
						: "";
				}
				echo " />\n";
			}
		}
		return true;
	}

	function dumpHeaders($identifier, $multi_table = false) {
		if ($_POST["format"] == "flat-xml-dataset") {
			header("Content-Type: text/xml; charset=utf-8");
			return "xml";
		}
	}

}