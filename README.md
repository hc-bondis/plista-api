## plista GmbH API Core Package for PHP

This package is the core API package which are used by plista API Development packages

### Requirements

This package works on PHP 5.5 but will probably work on most other PHP versions

### Dependencies

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
