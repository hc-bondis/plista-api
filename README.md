## plista GmbH API Core Package for PHP

This package is the core API package which are used by plista API Development packages

### Requirements

This package works on PHP 5.5 but will probably work on most other PHP versions

### Dependencies

This package does not depend on any other packages.

The https://github.com/plista/statistics-dev package depends on this package

### Installation

 1. Clone the package into a folder where PHP expects your classes to reside.
 2. Include the autoloader.php file in your code, before you intend to use classes which could depend on these classes.

#### Finding the correct location to clone your package

To figure out where PHP expects your files, you could execute this function in php:
```php
$paths = ini_get('include_path');
var_dump($paths);
```
In my circumstances, PHP expects my classes in **.:/usr/share/php/**

Paths are seperated by **:**, so we have actually two locations where PHP will look for class files:
 * . (the current folder relative to the executing script)
 * /usr/share/php/

In my case - the correct location is /usr/share/php/ so I change into that directory
```bash
cd /usr/share/php
```

#### Cloning the package

If you are now in the correct location to clone your package - do that

```bash
sudo git clone https://github.com/plista/plista-api.git 
```

If everything goes well, you should have a brand new plista-api folder created, with all the required files inside.

#### Documentation

Full documentation lies at https://statistics.plista.com/api_docs/index.html

#### Including the autoloader.php

```php
include_once("plista-api/autoloader.php");
```

At this point, when you include the above line of code early in you application startup, you will have access to all the Plista API Core classes.

In some cases, you could use these classes directly, but in other cases, when you are using for example the https://github.com/plista/statistics-dev package - the classes will be used without you being aware of it.

###Namespaces

All the classes live inside Plista namespaces - so they should not interfere with other classes.

When you want to use a class - be sure to include a ``use Plista\Namespace\Declaration\ClassName;``

Below is a definitive list of all Plista API Classes and their Namespaces

```
Plista\API\Interfaces\Response
Plista\API\Interfaces\ServiceDescription
Plista\API\Interfaces\API
Plista\API\Interfaces\Request
Plista\API\Interfaces\Service
Plista\API\Exception
Plista\API\Request\Http
Plista\API\Response\StdArray
Plista\API\Response\DataTables
Plista\API\Response\XML
Plista\API\Response\JSON
Plista\API\Response\MySQL
Plista\API\Response\StdObject
```

###Response Objects
The response objects all have certain private members which are accessible via their accessor functions:

```php
$response = $api->getCampaignStatistics(...);

$response->getResult();		//0 for failure, 1 for success
$response->getData();		//returns the raw JSON data
$response->getInfo();		//returns some interesting information about the request - time it took, size of the request, speeds, etc.
$response->getError();		//gets an error message and hash code for tracking down bugs
$response->getStatusCode();	//200 = success, 403 = access denied, 500 = internal server error, etc.
$response->getAPIToken();	//returns the API token which you received by logging in.
```

Lets say - that you will be getting some JSON from one of our servers, and you wish to convert this JSON into an object which is more suited to your needs.

In this case - you should consider creating a 'Custom Response Object'.

If you want to create your own 'Custom Response Objects' - you should extend the 'Response' interface living in

```bash
plista-api/classes/Plista/API/Interfaces/Response.php
```

When you extend from this class, you automatically inherit the functions above, plus you are free to declare any member variables you wish, and you have to provide an implementation for the ``process()`` function

In the example below - lets say we will be receiving some JSON representing some Pizza information (types of Pizza and so on) - and we want to convert these into Pizza objects. 
####Custom Response Object Example####

```php

use Plista\API\Interfaces\Response;

/**
 * By extending the Response interface, we can access the raw JSON data 
 * via $this->getData();
 */
class PizzaResponse extends Response {

	/**
	 * We can declare any member variables here, and because all the other member 
	 * variables are private, they can even have the same names as declared in the
	 * abstract Response class
	 */

	/**
	 * The name of the Pizza
	 * @var string $name
	 */
	public $name;
	
	/**
	 * An array of strings representing the ingredients for this pizza
	 * @var array $ingredients
	 */
	public $ingredients

	...
	// and so on
	...

	/**
	 * We also have to provide an implementation for the process() function, which
	 * essentially just converts the JSON
	 * into the type of object you just declared.
	 */
	public function process()
	{
		/**
		 * Convert the JSON data into an Array
		 */
		$data = json_decode($this->getData(), true);

		$this->name = $data["name"];

		$this->ingredients = array();

		foreach ($data["ingredients"] as $ingredient) {
			array_push($this->ingredients, $ingredient)
		}

		...
		// and so on
	}
}
```

Now that we have declared our 'Custom Response Object' class, we should tell our API which type of 'Custom Response Object' we expect from the call:

```
	$api = new PizzaAPI();	//An API Class which extends the plista-api/classes/Plista/API/Interfaces/API.php
	
	$api->setCustomResponseClass("PizzaResponse");

	$response = $api->getPizza(0);

	/**
	 * Now - at this point $response is an object of class "PizzaResponse" declared above
	 */
	echo $response->name;	//outputs the name of the pizza

	foreach ($response->ingredients as $ingredient) {
		... //and so on
``` 

Store your response object in ``plista-api/classes/Plista/API/Response`` and remember to include your response class in the ``plista-api/autoloader.php``

Simple as that!

###Custom API Classes (mainly relevant to Plista internal)

At some point you probably want to create a new API based on the plista-api core, because, lets say we get a new idea and want to make some part of Plista available via API.

You have to extend

```
plista-api/classes/Plista/API/Interfaces/API.php
```

and provide and implementation for ``listCalls()`` function which returns an array with some info about your API.

A good idea would also be to provide a 'Service' interface which lists the functions available in your API.

So your class signature would look like this :

```php

use \Plista\API\Interfaces\API;

interface Service {
	
	/**
	 * Returns a pizza with name and ingredients
	 * @param int $id of the pizza
	 */
	public function getPizza($id);

	...

	//and so on
}

/**
 * extend the API and declare a custom Service interface
 */
class PizzaAPI extends API implements Service {
	
	/**
	 * Provide implementation for listCalls()
	 */
	public function listCalls() {
		//return some info about your API
	}

	/**
	 * Provide implementation for getPizza()
	 */
	public function getPizza($id) {
		return $this->call(
			"/pizzas/get/$id"
		);
	}
}

```

The constructor for your API expects a URL to which the ``call()`` function will append its arguments

The second argument to ``call()`` is an optional array of 'key' => 'value' pairs, which will be converted into JSON POST payload.

###Custom Request Classes

At the moment we only support the HTTP request method. We could theoretically support any type of request - such as websocket requests, FTP request, SMTP requests and so on.

In order to extend the API to support more than one request - simply extend the interface at 

```
plista-api/classes/Plista/API/Interfaces/Request.php
```

Store your class at
```
/usr/share/php/plista-api/classes/Plista/API/Request
```

and remember to update the ```plista-api/autoloader.php``` with the path to your request object.

You will have to provide an implementation for ```send()``` function - which should be blocking and return a Response object which extends ```Plista\API\Interfaces\Response.php```

You also will have to extend the API interface and override the 'call()' method to use your new request. 

###Error handling
When an error occurs on the platform to which you are making an API call, the error should be converted into understandable JSON.

In some circumstances, it is not possible to properly convert the error information into understandable JSON, because of the nature of some types of errors.

In most cases however, when an error occurs, your response object should contain the following information:

####When an exception occurs

```php
$response = $api->getCampaignStatistics(...);

/**
 * Now an Exception occurs on the server, you can get error information like this
 */

$error = $response->getError();

/**
 * $error is an Array with the following information :
 */

$error = array(
	"hash" => "..."		// A random generated string which you can send us to track down the error
	"message" => "..."	// A message indicating an error occurred
);

```
####When an internal error occurs

```php
$response = $api->getCampaignStatistics(...);

/**
 * Now an internal error occurs on the server, unfortunately, there is not much helpful info
 */

$error = $response->getError();

/**
 * $error is an Array with the following information :
 */

$error = array(
	"result" => 0
	"message" => "Internal Server Error"
);
```

We would however receive a message, indicating that an error occurred and we will check it out. However, feel free to create an Issue on GitHub or contact us mailto:info@plista.com ;)
