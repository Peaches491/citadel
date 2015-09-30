<?php
//require '../app/vendor/autoload.php';
require '../app/utils.php';

$services = Utils::load_services();

foreach($services as $srv) {
  if($srv->name == "Plex") {
    $plex_srv = $srv;
  }
}

if(!$plex_srv) {
  echo "No Plex service defined!";
  return;
}

$image_url = $_GET['img'];
$network = $plex_srv->machine['ip'];
$plexAddress = $plex_srv->link;
$addressPosition = strpos($image_url, $plexAddress);

if($image_url) {
	$image_src = $plexAddress . $image_url . '?X-Plex-Token=' . $plex_srv->api_key;

  header('Content-type: image/jpeg');
  //header("Content-Length: " . filesize($image_src));
  readfile($image_src);
} else {
  echo "Bad Plex Image Url";
}
?>
