<?php
require_once 'utils.php';
require_once 'models/service.php';

$string = file_get_contents("../json.json", true);
$json_a = json_decode($string, true);

$machines_json = $json_a["machines"];
$services_json = $json_a["services"];
unset($services_json['Machines']);

$services = Service::from_ini($machines_json, $services_json);

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
