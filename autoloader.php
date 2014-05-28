<?php

class PlistaAPIAutoloader {

	/**
	 * We keep the classes in memory to prevent us from doing unecessary file checks
	 */
	public static $classes = array(
		'Plista\API\Interfaces\Response',
		'Plista\API\Interfaces\ServiceDescription',
		'Plista\API\Interfaces\API',
		'Plista\API\Interfaces\Request',
		'Plista\API\Interfaces\Service',
		'Plista\API\Exception',
		'Plista\API\Request\Http',
		'Plista\API\Response\StdArray',
		'Plista\API\Response\DataTables',
		'Plista\API\Response\XML',
		'Plista\API\Response\JSON',
		'Plista\API\Response\MySQL',
		'Plista\API\Response\StdObject'
	);

	/**
	 * This function autoloads the class if its namespace mapping exists and matches a class in our array
	 * @param $className
	 */
	public static function load($className) {

		$className = ltrim($className, '\\');

		if (!in_array($className, PlistaAPIAutoloader::$classes, true)){
			return;
		}

		$fileName = 'plista-api' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;

		if ($lastNsPos = strrpos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}

		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		require($fileName);
	}
}

spl_autoload_register(array('PlistaAPIAutoloader', 'load'));

?>
