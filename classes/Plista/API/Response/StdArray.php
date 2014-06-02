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
	 * Class StdArray converts the JSON response into an Array
	 * @package Plista\API\Response
	 */
	class StdArray extends Response {

		/**
		 * Convert the JSON data into an Array
		 */
		public function process() {
			$this->data = json_decode($this->getData(), true);
		}
	}
}