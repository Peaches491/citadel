<?php
require '../app/vendor/autoload.php';

$app = new \Slim\Slim(array(
  'templates.path' => '../app/views/',
  'view' => new \Slim\Views\Twig()
));

$view = $app->view();
$view->parserOptions = array(
  'debug' => true,
  'cache' => dirname(__FILE__) . '/../cache'
);
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

require '../app/config.php';
require '../app/includes.php';
require '../app/routes.php';

//echo phpinfo();
//echo xdebug_get_profiler_filename();

$app->run();

?>
