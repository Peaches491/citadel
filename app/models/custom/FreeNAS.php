<?php

class FreeNAS extends Service
{
  var $rest = null;
  function __construct($machines, $srv_arr) {
    parent::__construct($machines, $srv_arr);
    $this->extended = true;
    $this->content_template = "components/custom/FreeNAS.twig";
    $this->https = true;
  }

  function evaluate_status() {
    parent::evaluate_status();

    $this->rest = Utils::rest_call($this->get_url("/api/v1.0/storage/volume/"), ['format' => 'json']);

    if($this->rest == false || $this->rest[0]['status'] != "HEALTHY") {
      $this->status = Status::to_array(Status::ERROR);
    }
    if($this->rest != false) {
      $this->status['text'] = $this->rest[0]['status'];
    }
  }

  function get_content() {
    $content = [];

    $rest = Utils::rest_call($this->get_url("/api/v1.0/storage/volume/"), ['format' => 'json']);
    if($rest) $content['volumes'] = $rest;

    $rest = Utils::rest_call($this->get_url("/api/v1.0/storage/disk/"), ['format' => 'json']);
    if($rest) $content['disks'] = $rest;

    $rest = Utils::rest_call($this->get_url("/api/v1.0/system/alert/"), ['format' => 'json']);
    if($rest) $content['alerts'] = $rest;

    return $content;
  }

}

?>
