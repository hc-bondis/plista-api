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
	use Plista\API\Interfaces\Response;

	/**
	 * Class StdObject - Converts JSON into a standard PHP object
	 * @package Plista\API\Response
	 */
	class StdObject extends Response {

		/**
		 * Converts JSON into a standard PHP object
		 */
		public function process() {
			$this->data = json_decode($this->data, false);
		}
	}
}