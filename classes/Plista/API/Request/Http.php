<?php
/**
 * Created by PhpStorm.
 * User: theunisjbotha
 * Date: 14/03/14
 * Time: 12:14
 */

namespace Plista\API\Request {

	/**
	 * Interface declarations
	 */
	use Plista\API\Interfaces\Request;
	use Plista\API\Interfaces\Response;

	/**
	 * Implementation declarations
	 */
	use Plista\API\Response\StdArray;
	use Plista\API\Response\StdObject;
	use Plista\API\Response\DataTables;
	use Plista\API\Response\JSON;
	use Plista\API\Response\MySQL;
	use Plista\API\Response\XML;
	use Plista\API\Exception;

	/**
	 * Our environment is by default 'production'
	 */
	if (!defined('PLISTA_API_ENVIRONMENT')) {
		define('PLISTA_API_ENVIRONMENT','production');
	}

	/**
	 * Our cookie domain is by default '.plista.com'
	 */
	if (!defined('PLISTA_API_COOKIE_DOMAIN')) {
		define('PLISTA_API_COOKIE_DOMAIN','.plista.com');
	}

	/**
	 * The Request Object which will be sent over API.
	 * @package Plista\API\Request
	 */
	class Http extends Request {

		/**
		 * The type of response, example TYPE_JSON, TYPE_ARRAY, etc.
		 * @var
		 */
		public $responseType;

		/**
		 * The language
		 * @var
		 */
		public $language;

		/**
		 * Charset of the document (UTF-8)
		 * @var
		 */
		public $charset;

		/**
		 * The host we're connecting to
		 * @var
		 */
		public $host;

		/**
		 * Holds the authorization information
		 * @var
		 */
		public $authorization;

		/**
		 * Holds the user agent string
		 * @var
		 */
		public $userAgent;

		/**
		 * The URL we're connecting to
		 * @var
		 */
		private $url;

		/**
		 * The data we're sending
		 * @var
		 */
		public $data;

		/**
		 * The HTTP method (GET, POST, etc)
		 * @var
		 */
		public $method;

		/**
		 * Cookies we'll send
		 * @var
		 */
		public $cookies;

		/**
		 * Path of the URL we're connecting to
		 * @var
		 */
		public $path;

		/**
		 * Construct a default Request object
		 * @param $host
		 * @param $path
		 * @param $__options
		 */
		public function __construct(
			$host,
			$path,
			$__options		//Sorry for the underscores but otherwise it would be confusing
		) {
			$options = array(
				"method"	=> "POST",
				"responseType"	=> Response::TYPE_JSON,
				"charset"	=> "UTF-8",
				"host"		=> $host,
				"path"		=> $path,
				"authorization"	=> null,
				"data"		=> null,
				"userAgent"	=> $_SERVER['HTTP_USER_AGENT'],
				"language"	=> "en",
				"type"		=> "api",
				"url"		=> $host . $path,
				"cookies"	=> array(),
				"customResponseClass" => null
			);

			$options = array_merge($options, $__options);

			/**
			 * If we do not have any authorization details, attempt to use
			 * the current user information in COOKIES (if any)
			 */
			if (!$options["authorization"]) {

				$userEmail = null;
				$userToken = null;
				$apiToken  = null;

				if (isset($_COOKIE["user_email"])) {
					$userEmail = $_COOKIE["user_email"];
				}

				if (isset($_COOKIE["user_token"])) {
					$userToken = $_COOKIE["user_token"];
				}

				if (isset($_COOKIE["api_token"])) {
					$apiToken = $_COOKIE["api_token"];
				}

				if ($userEmail || $userToken || $apiToken) {
					$options["authorization"] = array();
				}

				if ($userEmail) {
					$options["authorization"]["user_email"] = $userEmail;
				}

				if ($userToken) {
					$options["authorization"]["user_token"] = $userToken;
				}

				if ($apiToken) {
					$options["authorization"]["api_token"] = $apiToken;
				}
			}

			foreach ($options as $key => $value) {
				$this->$key = $value;
			}
		}

		/**
		 * Sets the request method
		 * @param $method
		 */
		public function setMethod($method) {
			$this->method = $method;
		}

		/**
		 * Gets the request method
		 * @return mixed
		 */
		public function getMethod() {
			return $this->method;
		}

		/**
		 * Sets the authorization object
		 * @param $authorization string
		 */
		public function setAuthorization($authorization) {
			$this->authorization = $authorization;
		}

		/**
		 * Sets the host
		 * @param $host string
		 */
		public function setHost($host) {
			$this->host = $host;
		}

		/**
		 * Gets the host
		 * @return string the target host of the endpoint
		 */
		public function getHost() {
			return $this->host;
		}

		/**
		 * Gets the authorization object
		 * @return string
		 */
		public function getAuthorization() {
			return $this->authorization;
		}

		/**
		 * Sets the charset
		 * @param $charset
		 */
		public function setCharset($charset) {
			$this->charset = $charset;
		}

		/**
		 * Gets the charset
		 * @return mixed
		 */
		public function getCharset() {
			return $this->charset;
		}

		/**
		 * Sets the format
		 * @param $format
		 */
		public function setResponseType($format) {
			$this->responseType = $format;
		}

		/**
		 * Gets the format
		 * @return mixed
		 */
		public function getResponseType() {
			return $this->responseType;
		}

		/**
		 * Sets the path
		 * @param $path
		 */
		public function setPath($path) {
			$this->path = $path;
		}

		/**
		 * Gets the path
		 * @return mixed
		 */
		public function getPath() {
			return $this->$path;
		}

		/**
		 * Sets the language
		 * @param $language
		 */
		public function setLanguage($language) {
			$this->language = $language;
		}

		/**
		 * Gets the language
		 * @return mixed
		 */
		public function getLanguage() {
			return $this->language;
		}

		/**
		 * Sets the user agent string
		 * @param $userAgent
		 */
		public function setUserAgent($userAgent) {
			$this->userAgent = $userAgent;
		}

		/**
		 * Gets the user agent string
		 * @return mixed
		 */
		public function getUserAgent() {
			return $this->userAgent;
		}

		/**
		 * Send the Request via HTTP to the endpoint.
		 * @return mixed|null|DataTables|MySQL|StdArray|StdObject
		 * @throws \Plista\API\Exception
		 */
		public function send() {

			/**
			 * First check, maybe we have an API token set in cookies?
			 */
			if (isset($_COOKIE["api_token"])) {
				$this->authorization["api_token"] = $_COOKIE["api_token"];
			}

			/**
			 * If we have an API token, we have to unset the user email and password
			 * We unset the password because it is not wise to send the password with every request
			 * We unset the email because the token contains the email address with which the request
			 * was authenticated. We unset the token because the user can also login with his token
			 */
			if (isset($this->authorization["api_token"])) {
				//	unset($this->authorization["user_email"]);
				//	unset($this->authorization["user_token"]);
				unset($this->authorization["user_password"]);
			}

			/**
			 * JSON encode this object
			 */
			$data = json_encode($this);
			$size = strlen($data);

			/**
			 * Allow us to xdebug curl requests - remember to set multiple debug sessions in phpstorm
			 * to 2 (or more)
			 */
			if (PLISTA_API_ENVIRONMENT == "development") {
				array_push($this->cookies, "XDEBUG_SESSION=1");
			}

			/**
			 * Also set authentication cookies
			 */
			if ($this->authorization) {

				if (isset($this->authorization["user_email"])) {
					array_push($this->cookies, "user_email=" . $this->authorization["user_email"]);
				}

				if (isset($this->authorization["user_token"])) {
					array_push($this->cookies, "user_token=" . $this->authorization["user_token"]);
				}
			}

			/**
			 * Initialize curl
			 */
			$ch = curl_init($this->url);

			/**
			 * We will do POST request only
			 */
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

			/**
			 * Set the POST data
			 */
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

			/**
			 * Don't include header info in response result - otherwise JSON can't parse
			 */
			curl_setopt($ch, CURLOPT_HEADER, true);

			/**
			 * CURL can't follow redirects for POST requests
			 */
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

			/**
			 * Only return the data, don't output to browser
			 */
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			/**
			 * Do not verify SSL certificates, (enables plain SSL)
			 */
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			/**
			 * Set cookies (if any)
			 */
			if (!empty($this->cookies)) {
				curl_setopt($ch, CURLOPT_COOKIE, implode("; ", $this->cookies));
			}

			/**
			 * Set our request type
			 */
			curl_setopt($ch, CURLOPT_HTTPHEADER,
				array(
					'Accept: application/json',		// We want to receive JSON
					'Content-Type: application/json',	// We will send JSON
					'Content-Length: ' . $size,		// The size of the data we're sending
					'Requested-With: plista-api-call'
				)
			);

			$verbose = null;

			/**
			 * Enable logging in 'dev' environment
			 */
			if (PLISTA_API_ENVIRONMENT !== "production") {
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				$verbose = fopen('php://temp', 'rw+');
				curl_setopt($ch, CURLOPT_STDERR, $verbose);
			}

			/**
			 * We have a result
			 */
			$result		= Request::RESULT_OK;
			$stackTrace	= null;
			$info		= null;
			$data		= null;

			$jsonData	= null;
			$headers	= null;
			$response	= null;

			/**
			 * Send the request
			 */
			try {
				$response = curl_exec($ch);
			} catch (\Exception $e){
				$jsonData = null;
				$result = Request::RESULT_ERROR;
				$stackTrace = $e->getTrace();
			}

			/**
			 * If we have a response, decode the data
			 */
			if ($response) {
				$jsonData = preg_replace("/^.*?\r\n\r\n/s", "", $response);
				$headers = preg_replace("/(^.*?)\r\n\r\n.*/s", "$1", $response);
			}

			/**
			 * See if we have an API token we're supposed to set
			 */
			$apiToken = null;

			if (preg_match("/.*Set-Cookie:.*api_token=(.*?);.*/msi", $headers)) {
				$apiToken = preg_replace("/.*Set-Cookie:.*api_token=(.*?);.*/msi","$1", $headers);
			}
			//$apiToken = "d8wweGU9Xbk0zMUMqCqTpIdzCkwubEmMT2hbez5JcP4kJPaF9FjH75Jw%2Bg1WWRbmmsZXYyV%2FVYqwAClO5rdqaigd9eIfwCZQfUvGz6aQPcoc3B%2FOSp9USkUEq9Xxsi4%2Fyso%3D";

			if ($apiToken) {
				/**
				 * API token timeout is ten minutes (ok one second less because it expires in redis)
				 */
				$timeout = time() + 599;

				/**
				 * Find the cookie domain
				 */
				$domain = PLISTA_API_COOKIE_DOMAIN;

				/**
				 * Also set a token
				 */
				setcookie("api_token", $apiToken, $timeout, "/", $domain, false);

			}
			/**
			 * Log the curl request in 'dev' environment
			 */
			if ($verbose) {
				rewind($verbose);
				$verboseLog = stream_get_contents($verbose);
				error_log($verboseLog);
			}

			/**
			 * If the request was successful, get some request info
			 */
			if ($result == Request::RESULT_OK) {

				/**
				 * Get some response info
				 */
				$statusCode	= curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$totalTime	= curl_getinfo($ch, CURLINFO_TOTAL_TIME);
				$connectTime	= curl_getinfo($ch, CURLINFO_CONNECT_TIME);
				$downloadSpeed	= curl_getinfo($ch, CURLINFO_SPEED_DOWNLOAD);
				$uploadSpeed	= curl_getinfo($ch, CURLINFO_SPEED_UPLOAD);
				$requestSize	= curl_getinfo($ch, CURLINFO_REQUEST_SIZE);
				$downloadSize	= curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
				$uploadSize	= curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_UPLOAD);
				$contentType	= curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

				/**
				 * Store the info in a handy place
				 */
				$info = array(
					"statusCode"	=> $statusCode,
					"totalTime"	=> $totalTime,
					"connectTime"	=> $connectTime,
					"downloadSpeed"	=> $downloadSpeed,
					"uploadSpeed"	=> $uploadSpeed,
					"requestSize"	=> $requestSize,
					"downloadSize"	=> $downloadSize,
					"uploadSize"	=> $uploadSize,
					"contentType"	=> $contentType
				);

			}

			/**
			 * Close the curl handle
			 */
			curl_close($ch);

			/**
			 * We will now create the response
			 */
			$response = null;

			/**
			 * Do a quick check if any errors occurred server side
			 */
			$rawData = json_decode($jsonData, true);
			if (
				is_array($rawData) &&
				isset($rawData["result"]) &&
				!$rawData["result"]
			) {
				/**
				 * The API request succeeded, but some error occurred
				 */
				$response = new StdArray(
					$result,
					$jsonData,
					$info,
					$stackTrace
				);

				$response->info["message"] = $rawData["message"];
				return $response;
			}

			/**
			 * Do we create a standard JSON response, or process the data some more?
			 */
			switch ($this->responseType) {
				case Response::TYPE_ARRAY :
					$response = new StdArray(
						$result,
						$jsonData,
						$info,
						$stackTrace
					);
					break;
				case Response::TYPE_STD_OBJECT :
					$response = new StdObject(
						$result,
						$jsonData,
						$info,
						$stackTrace
					);
					break;
				case Response::TYPE_MYSQL :
					$response = new MySQL(
						$result,
						$jsonData,
						$info,
						$stackTrace
					);
					break;
				case Response::TYPE_DATATABLES :
					$response = new DataTables(
						$result,
						$jsonData,
						$info,
						$stackTrace
					);
					break;
				case Response::TYPE_XML :
					$response = new XML(
						$result,
						$jsonData,
						$info,
						$stackTrace
					);
					break;
				case Response::TYPE_CUSTOM :

					$className = $this->getCustomResponseClass();

					if (!$className) {
						throw new Exception("Custom response type, however Plista\\API\\Interfaces\\Request::\$customResponseClass not set");
					}

					/**
					 * Create a new Custom Response object
					 */
					try {
						$response = new $className(
							$result,
							$jsonData,
							$info,
							$stackTrace
						);
					} catch (\Exception $e) {
						throw new Exception($e->getMessage());
					}
					break;
				default:
					$response = new JSON(
						$result,
						$jsonData,
						$info,
						$stackTrace
					);
			}

			/**
			 * Process the response data into proper format
			 */
			$response->process();

			/**
			 * Return the response object
			 */
			return $response;
		}
	}
}