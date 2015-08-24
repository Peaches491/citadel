<?php

$app->get('/', function () use ($app, $js_sources, $css_sources) {
  $app->render('layout.twig', array(
    'title' => 'Home Base',
    'js' => $js_sources,
    'css' => $css_sources
  ));  
});

?>
