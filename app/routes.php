<?php
require_once 'utils.php';
require_once 'models/service.php';

$services = Utils::load_services();

$app->get('/', function () use ($app, $js_sources, $css_sources, $services) {

  foreach($services as &$srv) {
    $srv->evaluate_content();
    $srv->evaluate_status();
  }

  global $mobile;
  $app->render('layout.twig', array(
    'title' => 'Citadel',
    'js' => $js_sources,
    'css' => $css_sources,
    'services' => $services,
    'client' => ['is_mobile' => $mobile ]
  ));  
});

?>
