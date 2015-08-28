<?php
require_once 'utils.php';
require_once 'models/service.php';

$service_ini = parse_ini_file('../config.ini', true);

$machines = $service_ini['Machines'];
unset($service_ini['Machines']);

$services = Service::from_ini($machines, $service_ini);

$app->get('/', function () use ($app, $js_sources, $css_sources, $services) {

  foreach($services as &$srv) {
    $srv->evaluate_status();
  }

  $app->render('layout.twig', array(
    'title' => 'Home Base',
    'js' => $js_sources,
    'css' => $css_sources,
    'services' => $services
  ));  
});

?>
