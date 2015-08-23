<?php

$app->get('/', function () {
  echo "Hello, asshole.";
});
$app->get('/hello/:name', function ($name) {
  echo "Hello, $name";
});

?>
