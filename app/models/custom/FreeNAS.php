<?php

function build_sorter($a, $b) {
  $key = 'used_pct';
  return $b[$key] - $a[$key];
}

function recursive_usort(&$array, $key) {
  foreach ($array as $idx => &$value) {
    if(array_key_exists('children', $value)) {
      recursive_usort($value['children'], $key);
    }
  }
  return usort($array, "build_sorter");
}

class FreeNAS extends Service
{
  var $rest = null;
  function __construct($machines, $srv_arr) {
    parent::__construct($machines, $srv_arr, __file__);
    $this->extended = true;
    $this->content_template = "components/custom/FreeNAS.twig";
    $this->https = true;
  }

  function evaluate_status() {
    parent::evaluate_status();

  }

  function populate_content() {
    $rest = Utils::rest_call($this->get_url("/api/v1.0/storage/volume/"), ['format' => 'json']);
    if($rest) {
      recursive_usort($rest[0]['children'], 'used_pct');
      $this->content['volumes'] = $rest;
    } else {
      return;
    }

    if($rest == false || $rest[0]['status'] != "HEALTHY") {
      $this->status = Status::to_array(Status::ERROR);
    }
    if($rest != false) {
      $this->status['text'] = $rest[0]['status'];
    }

    $this->content['vol_status'] = [];
    foreach ($this->content['volumes'] as $key => $volume) {
      $rest = Utils::rest_call($this->get_url('/api/v1.0/storage/volume/'.$volume['id'].'/status/'), ['format' => 'json']);
      if($rest) {
        $this->content['volumes'][$key]['vdev_status'] = $rest[0];
      }
    }

    $rest = Utils::rest_call($this->get_url("/api/v1.0/storage/disk/"), ['format' => 'json']);
    if($rest) $this->content['disks'] = $rest;

    $rest = Utils::rest_call($this->get_url("/api/v1.0/system/alert/"), ['format' => 'json']);
    if($rest) $this->content['alerts'] = $rest;

    $rest = Utils::rest_call($this->get_url("/api/v1.0/system/version/"), ['format' => 'json']);
    if($rest) $this->content['version'] = $rest;

    $this->content['subtitle'] = $this->content['version']['fullversion'];

    return $this->content;
  }

}

?>
