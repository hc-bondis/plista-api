<?php

/**
 * Created by PhpStorm.
 * User: theunisjbotha
 * Date: 14/03/14
 * Time: 12:14
 */

namespace Plista\API\Interfaces {

	/**
	 * Our API uses HTTP requests (implemented with php curl)
	 */
	use Plista\API\Request\Http;

	/**
	 * This is the base class for all remote controllers that offer service functionality over a network
	 * @package Plista\API
	 */
	abstract class API implements Service {

		/**
		 * @var - base URL where API call will be made to
		 */
		protected $baseURL;

		/**
		 * @var null - Authorization object which is required for the request
		 */
		protected $authorization;

		/**
		 * @var bool - default false. When set to true, will perform authorization during construction
		 */
		protected $authorize;

		/**
		 * @var boolean - true if authorized, false if not
		 */
		protected $authorized;

		/**
		 * @var Response::TYPE_JSON by default, check out the Response class for possible values
		 */
		protected $responseType;

		/**
		 * Set this equal to a "ClassName" string which implements Plista\API\Interfaces\Response.
		 * In the process() function of this class, convert the JSON into your ClassName
		 * @var
		 */
		protected $customResponseClass = null;

		/**
		 * Sets the classname to which the JSON response will be converted to
		 * @param $className
		 * @return mixed
		 */
		public function setCustomResponseClass($className) {
			$this->customResponseClass = $className;
		}

		/**
		 * Returns the name of the class to which your response JSON will be converted to
		 * @return mixed
		 */
		public function getCustomResponseClass() {
			return $this->customResponseClass;
		}

		/**
		 * The "authorization" array in $options is optional, and required when you are not making API
		 * calls from within a 'plista' platform.
		 *
		 * You could use the following combinations to authorize:
		 *
		 * "user_email" and "user_password" -> your Plista credentials
		 *
		 * OR
		 *
		 * "user_email" and "user_token" -> your cookies which exist when logged into a 'plista' platform
		 *
		 * OR
		 *
		 * "api_token" -> your API token which you receive in response header after a successful API
		 *
		 * OR
		 *
		 * nothing - when you are working on a plista platform and are already logged in.
		 *
		 * When 'authorize' is true, there will be an automatic, explicit authorization request sent on API construction.
		 * This is an additional request and not required, because authentication data will be passed on to the platform
		 * with the first 'data' request, which will succeed if the right credentials were supplied.
		 *
		 * The responseType is the ultimate format of the response "data" member which you receive from the request.
		 * By default it is a JSON string, but could be an Array, or a PHP standard Object, or MySQL query.
		 *
		 * More types could become available
		 *
		 * When you authorize successfully - you receive an API token in the header which you can use for further requests.
		 *
		 * This API token is stored in a cache and is valid for 10 minutes (for now at least) - after which you would
		 * need to re-authenticate
		 *
		 * @param null $baseUrl This is the URL to which the API calls will be made
		 * @param null $options = array(
		 * 	"authorization" => array (
		 * 		"user_email" => "someone@domain.com"
		 * 		"user_password" => "your_password"
		 * 		OR
		 * 		"user_email" => "someone@domain.com"
		 * 		"user_token" => "your_token_from_cookie"
		 * 		OR
		 * 		"api_token" => "your_encrypted_api_token_from_header"
		 * 		OR
		 * 		do not include this option when already logged into a plista platform
		 * 	),
		 * 	"authorize" => true | false
		 * 	"responseType" => Response::TYPE_JSON (0) |
		 * 			  Response::TYPE_ARRAY (1) |
		 * 			  Response::TYPE_STD_OBJECT (2) |
		 *			  Response::TYPE_MYSQL (3)
		 *
		 * )
		 */
		function __construct(
			$baseUrl = null,
			$options = null
		) {

			$this->baseURL		= $baseUrl;

			if (!$options) {
				$options = array();
			}

			$options = array_merge(
				array(
					"authorization"		=> array(),
					"authorize"		=> false,
					"responseType"		=> Response::TYPE_JSON,
					"customResponseClass"	=> null
				),
				$options
			);

			foreach ($options as $key => $value) {
				$this->$key = $value;
			}

			if ($this->authorization && $this->authorize) {
				$this->authorized = $this->authorize();
			}
		}

		/**
		 * Calls the 'send()' method of the 'Request' implementation
		 * @param $action
		 * @param $data
		 * @param bool $forceSSL
		 * @return Response
		 */
		protected final function call($action, $data = null, $forceSSL = false) {

			$baseURL = $this->baseURL;

			if ($forceSSL) {
				$baseURL = preg_replace("/^http:/", "https:", $baseURL);
			}

			/**
			 * Create a new Request object
			 */
			$req = new Http(
				$baseURL,
				$action,
				array(
					"authorization"		=> $this->authorization,
					"data"			=> $data,
					"responseType"		=> $this->responseType,
					"customResponseClass"	=> $this->customResponseClass
				)
			);

			/**
			 * Send the request and return the Response object
			 */
			return $req->send();
		}

		/**
		 * Authorizes the user and sets the API token on this API object
		 * @return bool (false when authorization fails, true when it succeeds)
		 */
		public function authorize() {

			/**
			 * Check if we have an authorization object
			 */
			if (!$this->authorization) {
				return false;
			}

			/**
			 * If we don't have a baseURL to authenticate against, return false
			 */
			if (!$this->baseURL) {
				return false;
			}

			$response = null;

			/**
			 * Backup the Response::TYPE
			 */
			$responseType = $this->responseType;

			/**
			 * If we have a user email and token, we have enough credentials to proceed
			 */
			if (
				isset($this->authorization["user_email"]) &&
				isset($this->authorization["user_token"])
			) {
				/**
				 * We're expecting an array of data
				 */
				$this->responseType = Response::TYPE_ARRAY;

				/**
				 * Call the login method of the Authentication Platform
				 */
				$response = $this->call(
					"/authorization/login",
					array(
						"user_email" => $this->authorization["user_email"],
						"user_token" => $this->authorization["user_token"]
					),
					true
				);

			}

			/**
			 * If we have a user email and password, we need to login first to get a token
			 */
			if (
				isset($this->authorization["user_email"]) &&
				isset($this->authorization["user_password"])
			) {
				/**
				 * We're expecting an array of data
				 */
				$this->responseType = Response::TYPE_ARRAY;

				/**
				 * Call the login method of the Authentication Platform
				 */
				$response = $this->call(
					"/authorization/login",
					array(
						"user_email"    => $this->authorization["user_email"],
						"user_password" => $this->authorization["user_password"]
					),
					true
				);

			}

			/**
			 * Restore the original responseType
			 */
			$this->responseType = $responseType;

			/**
			 * Did we manage to make an API login call?
			 */
			if (!$response) {
				return false;
			}

			/**
			 * Did we get a token?
			 */
			if (isset($response->data["apiToken"])) {

				/**
				 * Cool, set the token
				 */
				$this->setApiToken($response->data["apiToken"]);

				/**
				 * We're authorized
				 */
				return true;
			}

			/**
			 * We're not authorized,
			 */
			return false;

		}

		/**
		 * Convenience function to set the API token
		 * @param $apiToken
		 */
		public function setApiToken($apiToken) {
			$this->authorization["api_token"] = $apiToken;
		}

		/**
		 * If you are authorized, it returns the current API token.
		 *
		 * If you are not authorized, it authorizes you first and then returns the API token
		 * @return mixed
		 */
		public function getApiToken() {

			if (!$this->authorization) {
				return null;
			}

			if (isset($this->authorization["api_token"])) {
				return $this->authorization["api_token"];
			}

			if ($this->authorize()) {
				return $this->authorization["api_token"];
			}

			return null;
		}
	}
}
