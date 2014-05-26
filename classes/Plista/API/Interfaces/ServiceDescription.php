<?php
/**
 * Created by PhpStorm.
 * User: theunisjbotha
 * Date: 14/03/14
 * Time: 12:14
 */

namespace Plista\API\Interfaces {

	/**
	 * Base class for all service descriptions.
	 * @package Plista\API\Interfaces
	 */
	interface ServiceDescription {

	//	private $name;

		/**
		 * Set the name and version of this API
		 * @param $name
		 */
	//	public function setName($name) {
	//		$this->name = $name;
	//	}

		/**
		 * Returns the name (and version) of this API
		 * @return mixed
		 */
	//	public function getName() {
	//		return $this->name;
	//	}

		/**
		 * Lists the functions and arguments available through this API
		 * @return mixed
		 */
		public function listCalls();
	}
}
