<?php 

use Sophokles\Controller\controller;

session_start();
ini_set('display_errors', '1');

$baseDir = __DIR__.'/';
define('BASEDIR', $baseDir);

require_once $baseDir.'system/autoloader.php';
require_once $baseDir.'vendor/autoload.php';

$controler = new controller();

$controler->execute();

