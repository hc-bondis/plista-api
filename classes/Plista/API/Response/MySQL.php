<?php
/**
 * Created by PhpStorm.
 * User: theunisjbotha
 * Date: 14/03/14
 * Time: 12:14
 */

namespace Plista\API\Response {

	/**
	 * Interface declarations
	 */
	use \Plista\API\Interfaces\Response;

	/**
	 * Class MySQL converts the JSON response into a MySQL query, ready to be executed on a server
	 * @package Plista\API\Response
	 */
	class MySQL extends Response {

		/**
		 * We expect the data to be in $this->data = array(
		 * 	0 => array(
		 * 		"columnName" => "value"
		 * 		"columnName2" => "value"
		 * 		...
		 * 		n
		 * 	)
		 * 	1 => array(
		 * 	)
		 * 	...
		 * 	n
		 * )
		 */
		public function process() {

			/**
			 * Decode all data from JSON
			 *
			 * @var array $data
			 */
			$data = json_decode($this->data, true);

			/**
			 * Get keys from first row of data set
			 *
			 * @var array $columNames
			 */
			$columnNames = array_keys($data[0]);

			/**
			 * Generate tablename from given columns
			 *
			 * @var string $tableName
			 */
			$tableName = "tmp_plista_api_" . md5(implode($columnNames));

			/**
			 * Building the query for creating the temporary Table
			 * Note: This function does not fires a DROP TABLE. If the
			 * table already exists the data gets filled in again. So the
			 * client is responsible for droping the table after usage
			 *
			 * @var string $sql
			 */
			$sql = "CREATE TABLE IF NOT EXISTS `" . $tableName . "`
			(" . implode( $columnNames, " VARCHAR(255), " ) . " VARCHAR(255))
			ENGINE=MEMORY
			DEFAULT CHARSET=utf8;";

			/**
			 * Build the query for inserting data into the temporary table
			 *
			 * @var string $sql
			 */
			foreach ($data as $row) {
				$sql .= "\nINSERT INTO $tableName (" . implode($columnNames, ", ") . ") VALUES ('" . implode($row, "', '") . "');";
			}

			/**
			 * set the data
			 */
			$this->setData(
				array(
					"table_name"	=> $tableName,
					"query"		=> $sql
				)
			);
		}
	}
}