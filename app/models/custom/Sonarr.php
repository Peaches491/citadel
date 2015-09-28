<?php

class Sonarr extends Service
{
  var $rest = null;
  function __construct($machines, $srv_arr) {
    parent::__construct($machines, $srv_arr, __file__);
    $this->extended = true;
    $this->content_template = "components/custom/Sonarr.twig";
  }
  
  function pre_render() {
    //var_dump($this->content['history']['records']);
  }

  function evaluate_status() {
    parent::evaluate_status();

    $this->rest = Utils::rest_call($this->get_url("/api/system/status"), ['apikey' => $this->api_key]);

    if($this->rest == false) {
      $this->status = Status::to_array(Status::ERROR);
    }
    if($this->rest != false) {
      $this->status = Status::to_array(Status::ONLINE);
    }
  }

  function populate_content() {
    $rest = Utils::rest_call($this->get_url("/api/system/status"), ['apikey' => $this->api_key]);
    if( $rest ) {
      $this->content['status'] = $rest;
      $this->content['subtitle'] = $rest['branch'] .' - '. $rest['version'];
    }

    $next_week = implode('-', array_slice(explode('-', date('c', time() + 604800)), 0, -1)).'Z';
    $rest = Utils::rest_call($this->get_url("/api/calendar"), 
     ['apikey' => $this->api_key,
      'end' => $next_week]);
    if( $rest ) {
      $this->content['calendar'] = $rest;
    }

    $rest = Utils::rest_call($this->get_url("/api/history"),
      ['apikey' => $this->api_key,
       'page' => 1,
       'pageSize' => 10,
       'sortKey' => 'date',
       'sortDir' => 'desc']);
    if( $rest ) {
      $this->content['history'] = $rest;
    }

    $rest = Utils::rest_call($this->get_url("/api/queue"), ['apikey' => $this->api_key]);
    if( $rest ) {
      $this->content['queue'] = $rest;
    }

    return $this->content;
  }

}

?>
