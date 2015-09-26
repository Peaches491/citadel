<?php
require_once __DIR__.'/../utils.php';
require_once __DIR__.'/../cachable.php';
require_once 'status.php';
require_once 'custom/FreeNAS.php';
require_once 'custom/Sonarr.php';

class Service
{
  var $name = '<Service>';
  var $machine = array(
    'name' => '<Machine>',
    'ip' => '127.0.0.1'
  );
  var $port = 0;
  var $status = [];
  var $link = "";
  var $body = "";
  var $subtitle = "";
  var $extended = false;
  var $auth = false;
  var $https = false;
  var $api_key = NULL;
  var $content = [];

  function __construct($machines, $srv_arr, $hash_source = __file__) {
    $this->status = Status::to_array(Status::UNKNOWN);
    $this->body = '';
    $this->name = $srv_arr["name"];
    $this->machine = [
      'name' => $srv_arr['machine'],
      'ip' => $machines[$srv_arr['machine']]
    ];

    if(array_key_exists('port', $srv_arr)) {
      $this->port = $srv_arr['port'];
    }
    if(array_key_exists('https', $srv_arr)) {
      $this->https = true;
    } else {
      $this->https = false;
    }
    if($this->https) {
      $this->link = 'https://' . $this->machine['ip'] . ':' . $this->port;
    } else {
      $this->link = 'http://' . $this->machine['ip'] . ':' . $this->port;
    }

    if(array_key_exists('api_key', $srv_arr)) {
      $this->api_key = $srv_arr['api_key'];
    }
    if(array_key_exists('auth', $srv_arr)) {
      $this->auth = $srv_arr['auth'];
    }

    $this->cache = new Cachable($this->name, 60, $hash_source,
      array($this, 'populate_content'));
    //$this->ping_cache = new Cachable('ping_'.$this->name, 60, __file__,
      //array($this, 'populate_content'));
    $this->content = [];
  }

  function pre_render() {}

  function evaluate_content() {
    //$this->cache->update_cache();
    $this->content = $this->cache->get();
    $this->pre_render();
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
    if($this->port) $url = $url.':'.$this->port;
    $url = $url.$path;

    return $url;
  }

  function populate_content() {
    //$this->cache->update_cache();
  }

  static function from_ini($machines, $service_ini) {
    $services = [];
    foreach ($service_ini as $srv_arr) {
      unset($type);
      $type = false;
      if( array_key_exists('type', $srv_arr))
        $type = $srv_arr['type'];
      switch($type ?: 'none') {
        case 'Sonarr':
          $srv = new Sonarr($machines, $srv_arr);
          break;
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
