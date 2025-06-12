<?php

/** Dump to XML format specifically PHPUnit's XML DataSet structure <dataset><table name=""><column>name</column><row><value>value</value></row></table></dataset>
* @link http://www.adminer.org/plugins/#use
* @link http://phpunit.de/manual/3.7/en/database.html#database.available-implementations
* @author Michal Brašna
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerDumpXmlDataSet {
	/** @access protected */
	var $dataset = false;
	
	function dumpFormat() {
		return array('xml-dataset' => 'XML DataSet');
	}

	function dumpTable($table, $style, $is_view = false) {
		if ($_POST["format"] == "xml-dataset") {
			return true;
		}
	}
	
	function _dataset() {
		echo "</dataset>\n";
	}
	
	function dumpData($table, $style, $query) {
		if ($_POST["format"] !== "xml-dataset") {
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
			echo "\t<table name='" . Adminer\h($table) . "'>\n";
			
			$query2 = "SHOW FULL COLUMNS FROM " . Adminer\table(Adminer\DB) . "." . Adminer\table($table);
			$result2 = $connection->query($query2);
			if ($result2) {
				while ($row2 = $result2->fetch_row()) {
					echo "\t\t<column>" . Adminer\h($row2[0]) . "</column>\n";
				}
			}
				
			while ($row = $result->fetch_assoc()) {
				echo "\t\t<row>\n";
				foreach ($row as $val) {
					echo isset($val)
						? "\t\t\t<value>" . Adminer\h($val) . "</value>\n"
						: "\t\t\t<null />\n";
				}
				echo "\t\t</row>\n";
			}
			echo "\t</table>\n";
		}
		return true;
	}

	function dumpHeaders($identifier, $multi_table = false) {
		if ($_POST["format"] == "xml-dataset") {
			header("Content-Type: text/xml; charset=utf-8");
			return "xml";
		}
	}

}