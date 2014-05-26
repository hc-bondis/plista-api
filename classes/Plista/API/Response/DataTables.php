<?php
/**
 * Created by PhpStorm.
 * User: theunisjbotha
 * Date: 14/03/14
 * Time: 12:14
 */

namespace Plista\API\Response{

	/**
	 * Interface declarations
	 */
	use Plista\API\Interfaces\Response;

	/**
	 * Implemenation declarations
	 */
	use Plista\API\Exception;

	/**
	 * Class DataTables converts the JSON into JSON ready for DataTables
	 * @package Plista\API\Response
	 */
	class DataTables extends Response {

		/**
		 * Converts the JSON into JSON ready for Datatables
		 * @throws Exception
		 */
		public function process() {
			throw new Exception("Implement Plista\\API\\Response\\DataTables::process()");
		}
	}
}