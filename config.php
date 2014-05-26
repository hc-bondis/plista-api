<?php
/**
 * Created by PhpStorm.
 * User: theunisjbotha
 * Date: 14/03/14
 * Time: 12:14
 */

Plista::defvar("api/ENVIRONMENT", "production");
Plista::defvar("api/COOKIE_DOMAIN", ".plista.com");

$classloader = Plista::getClassLoader();
$classloader->register('Plista\\API', __DIR__ . '/classes');