<?php
require '../app/vendor/autoload.php';

$app = new \Slim\Slim();
require '../app/config.php';
require '../app/routes.php';
$app->run();

?>
