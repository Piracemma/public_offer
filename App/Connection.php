<?php

namespace App;

class Connection {

	public static function getDb() {
		try {

			$conn = new \PDO(
				DB_CONFIG,
				DB_USER,
				DB_PASSWORD 
			);

			return $conn;

		} catch (\PDOException $e) {
			echo $e;
		}
	}
}

?>