<?php
include_once 'Config.php';

class Repo {
	public function __construct()
	{
		$config = new Config();
		$config = $config->getDb();
		$this->link = new mysqli($config['host'], $config['username'], $config['password'], $config['db']);
	}

	public function buildSql($properties = array())
	{
		$select = implode(', ', $properties['columns']);
		return <<<SQL
			SELECT {$select}
 			  FROM {$properties['table']}
  			 WHERE {$properties['table']}.{$properties['index']} = '{$properties['value']}'
SQL;
	}

	public function getArray($sql)
	{
		$data = array();
		if ($result = mysqli_query($this->link, $sql)) {
			while ($row = $result->fetch_assoc()) {
				$data[] = $row;
			}
		}

		return $data;
	}

	public function getArraySingle($sql)
	{
		$data = array();
		if ($result = mysqli_query($this->link, $sql)) {
			while ($row = $result->fetch_assoc()) {
				$data[] = $row;
			}
		}

		if (!empty($data)) {
			$temp_data = $data[0];
			$data = array();
			foreach ($temp_data as $key => $value) {
				$data[$key] = $value;
			}
		}

		return $data;
	}

	public function setSql($sql)
	{
		return mysqli_query($this->link, $sql);
	}
}

?>