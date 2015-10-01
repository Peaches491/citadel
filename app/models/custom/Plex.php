<?php

class Plex extends Service
{
  var $rest = null;
  function __construct($machines, $srv_arr) {
    parent::__construct($machines, $srv_arr, __file__);
    $this->extended = true;
    $this->content_template = "components/custom/Plex.twig";
  }

  function pre_render() {
    //var_dump($this->content['history']['records']);
  }

  function evaluate_status() {
    parent::evaluate_status();

  }

  function populate_content() {
    $this->content = [];

    $rest = Utils::rest_call($this->get_url("/status/sessions"),
      ['X-Plex-Token' => $this->api_key],
      ['Accept: application/json']);

    if($this->rest == false) {
      $this->status = Status::to_array(Status::ERROR);
    }
    if($rest != false) {
      $this->status = Status::to_array(Status::ONLINE);
      if(count($rest['_children']) > 0) {
        foreach( $rest['_children'] as $element ) {
          if( $element['_elementType'] == 'Video' ) {

            foreach( $element['_children'] as $subElement ) {
              if( $subElement['_elementType'] == 'Player' ) {
                if( $subElement['state'] == 'playing' ) {
                  $this->status = Status::to_array(Status::PLAYING);
                } else {
                  $this->status = Status::to_array(Status::PAUSED);
                }
              }
            }
          }
        }
      }
    }
    $this->content['sessions'] = $rest;

    $rest = Utils::rest_call($this->get_url("/servers"),
      ['X-Plex-Token' => $this->api_key],
      ['Accept: application/json']);
    $this->content['servers'] = $rest;
    if( $rest != false and count($rest['_children']) > 0) {
      $this->content['subtitle'] = $rest['_children'][0]['version'];
    }

    $rest = Utils::rest_call($this->get_url("/library/recentlyAdded"),
      ['X-Plex-Token' => $this->api_key,
       'X-Plex-Container-Size' => 3,
       'X-Plex-Container-Start' => 0],
      ['Accept: application/json']);
    $this->content['recentlyAdded'] = $rest;



    return $this->content;
  }

}

?>
