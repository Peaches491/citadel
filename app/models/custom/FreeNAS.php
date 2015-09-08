<?php

class FreeNAS extends Service
{
  function __construct($machines, $srv_arr) {
    parent::__construct($machines, $srv_arr);
    $this->extended = true;
    $this->content_template = "components/custom/FreeNAS.twig";
  }

  function evaluate_status() {
    parent::evaluate_status();

    $rest = Utils::rest_call("https://192.168.1.137/api/v1.0/storage/volume/", ['format' => 'json']);

    if($rest == false || $rest[0]->status != "HEALTHY") {
      $this->status = Status::to_array(Status::ERROR);
    }
    if($rest != false && $rest[0]->status != "HEALTHY") {
      $this->status['text'] = $rest[0]->status;
    }
  }

}

?>
