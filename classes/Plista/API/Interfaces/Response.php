<?php
/**
 * Created by PhpStorm.
 * User: theunisjbotha
 * Date: 14/03/14
 * Time: 12:14
 */

namespace Plista\API\Interfaces {

	/**
	 * Class Response - Is the base class for all types of responses we support
	 * @package Plista\API\Interfaces
	 */
	abstract class Response {

		/**
		 * The result of the request - 1 if success, 0 if failure
		 * @var int
		 */
		private $result;

		/**
		 * Holds the JSON string of the response
		 * @var string
		 */
		private $data;

		/**
		 * Holds lots of request info, like status code, time etc.
		 * @var
		 */
		private $info;

		/**
		 * Holds debugging information - however not yet implemented
		 * @var
		 */
		private $error;

		/**
		 * Status code of the request response
		 * @var int
		 */
		private $statusCode;

		/**
		 * API Token when you successfully authenticate
		 * @var string
		 */
		private $apiToken;

		/**
		 * Types of Responses we can / will handle
		 * When TYPE_CUSTOM - specify a custom class name which implements this (Plista\API\InterfacesResponse)
		 * interface
		 */
		const TYPE_JSON			= 0;
		const TYPE_ARRAY		= 1;
		const TYPE_STD_OBJECT		= 2;
		const TYPE_MYSQL		= 3;
		const TYPE_DATATABLES		= 4;
		const TYPE_XML			= 5;
		const TYPE_CUSTOM		= 6;

		/**
		 * The Response Constructor
		 * @param int $result
		 * @param null $data
		 * @param null $info
		 * @param null $error
		 * @param int $statusCode
		 * @param null $apiToken
		 */
		public function __construct(
			$result = 1,
			$data = null,
			$info = null,
			$error = null,
			$statusCode = 0,
			$apiToken = null
		) {
			$this->result		= $result;
			$this->data		= $data;
			$this->info		= $info;
			$this->error		= $error;
			$this->statusCode	= $statusCode;
			$this->apiToken		= $apiToken;
		}

		/**
		 * Gets the result
		 * @return mixed
		 */
		public function getResult() {
			return $this->result;
		}

		/**
		 * Sets the result
		 * @param $result
		 */
		public function setResult($result) {
			$this->result = $result;
		}

		/**
		 * Gets the data
		 * @return mixed
		 */
		public function getData() {
			return $this->data;
		}

		/**
		 * Sets the data
		 * @param $data
		 */
		public function setData($data) {
			$this->data = $data;
		}

		/**
		 * Returns an array with lots of useful information about the request.
		 * What exactly, depends on the Request implementation.
		 * @return mixed
		 */
		public function getInfo() {
			return $this->info;
		}

		/**
		 * Sets the info
		 * @param $info
		 */
		public function setInfo($info) {
			$this->info = $info;
		}

		/**
		 * Sets the error information
		 * @param $error
		 */
		public function setError($error) {
			$this->error = $error;
		}

		/**
		 * Gets the stack trace
		 * @return mixed
		 */
		public function getError() {
			return $this->error;
		}

		/**
		 * Gets the status code
		 * @return mixed
		 */
		public function getStatusCode() {
			return $this->statusCode;
		}

		/**
		 * Sets the status code
		 * @param $statusCode
		 */
		public function setStatusCode($statusCode) {
			$this->statusCode = $statusCode;
		}

		/**
		 * Gets the API Token
		 * @return string
		 */
		public function getAPIToken() {
			return $this->apiToken;
		}

		/**
		 * Sets the API Token
		 * @param $apiToken
		 */
		public function setAPIToken($apiToken) {
			$this->apiToken = $apiToken;
		}

		/**
		 * This function should process the JSON and turn it into whichever type its supposed to be
		 */
		public abstract function process();
	}
}