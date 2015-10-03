<?php
require_once 'utils.php';
require_once 'models/service.php';

$services = Utils::load_services();

function evaluate_services($services) {
  foreach($services as &$srv) {
    $srv->evaluate_content();
    $srv->evaluate_status();
  }
  return $services;
}

$app->get('/', function () use ($app, $js_sources, $css_sources, $services) {
  if(isset($_GET["forceClearCache"])) {
    Utils::clear_caches();
  }

  evaluate_services($services);

  global $mobile;
  $app->render('layout.twig', array(
    'title' => 'Citadel',
    'js' => $js_sources,
    'css' => $css_sources,
    'services' => $services,
    'client' => ['is_mobile' => $mobile ]
  ));  
});

$app->post('/', function () use ($app, $js_sources, $css_sources, $services) {
  if( filter_var($_POST['forceCacheClear'], FILTER_VALIDATE_BOOLEAN) ) {
    Utils::clear_caches();
  }

  $app->redirect('/');

});


?>
