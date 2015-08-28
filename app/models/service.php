<?php
require_once __DIR__.'/../utils.php';
require_once 'status.php';

class Service
{
  var $name = '<Service>';
  var $machine = array(
    'name' => '<Machine>',
    'ip' => '127.0.0.1'
  );
  var $port = 80;
  var $status = [];
  var $link = "";

  function __construct() {
    $this->status = Status::to_array(Status::UNKNOWN);
  }

  function evaluate_status() {
    try {
      $ping = Utils::ping($this->link);
      if ($ping > 0) {
        $this->status = Status::to_array(Status::ONLINE);
      } else {
        $this->status = Status::to_array(Status::OFFLINE);
      }
    } catch (Exception $e) {
      var_dump($e);
      $this->status = Status::to_array(Status::ERROR);
    }
  }

  static function from_ini($machines, $service_ini) {
    $services = [];
    foreach ($service_ini as $srv_name => $srv_arr) {
      $srv = new Service();
      $srv->name = $srv_name;
      $srv->machine = [
        'name' => $srv_arr['machine'], 
        'ip' => $machines[$srv_arr['machine']]
      ];
      if(array_key_exists('port', $srv_arr)) $srv->port = $srv_arr['port'];
      $srv->link = 'http://' . $srv->machine['ip'] . ':' . $srv->port;
      $services[] = $srv;
    }
    return $services;
  }
}


?>
