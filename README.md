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
