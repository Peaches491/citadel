<?php
require_once __DIR__.'/../utils.php';
require_once 'status.php';
require_once 'custom/FreeNAS.php';

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
  var $body = "";
  var $extended = false;
  var $auth = false;
  var $https = false;

  function __construct($machines, $srv_arr) {
    $this->status = Status::to_array(Status::UNKNOWN);
    $this->body = simplexml_load_file('http://www.lipsum.com/feed/xml?amount=2&what=paras&start=0')->lipsum;
    $this->name = $srv_arr["name"];
    $this->machine = [
      'name' => $srv_arr['machine'],
      'ip' => $machines[$srv_arr['machine']]
    ]; if(array_key_exists('port', $srv_arr)) $this->port = $srv_arr['port']; if(array_key_exists('https', $srv_arr)) $this->https = true; if($this->https) {
      $this->link = 'https://' . $this->machine['ip'] . ':' . $this->port;
    } else {
      $this->link = 'http://' . $this->machine['ip'] . ':' . $this->port;
    }

    if(array_key_exists('auth', $srv_arr)) {
      $this->auth = $srv_arr['auth'];
    }
  }

  function evaluate_content() {
    $this->content = $this->get_content();
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

  function get_url($path) {
    if($this->https) {
      $url = "https://";
    } else {
      $url = "http://";
    }
    if($this->auth) {
      $url = $url.$this->auth['username'].":".$this->auth['password']."@";
    }
    $url = $url.$this->machine['ip'];
    $url = $url.$path;

    return $url;
  }

  function get_content() {
    return [];
  }

  static function from_ini($machines, $service_ini) {
    $services = [];
    foreach ($service_ini as $srv_arr) {
      unset($type);
      $type = false;
      if( array_key_exists('type', $srv_arr))
        $type = $srv_arr['type'];
      switch($type ?: 'none') {
        case 'FreeNAS':
          $srv = new FreeNAS($machines, $srv_arr);
          break;
        default:
          $srv = new Service($machines, $srv_arr);
          break;
      }
      $services[] = $srv;
    }
    return $services;
  }
}


?>
