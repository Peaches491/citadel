<?php

abstract class Status {
  const UNKNOWN = 0;
  const ONLINE = 1;
  const OFFLINE = 2;
  const ERROR = 3;
  const UPLOADING = 4;
  const DOWNLOADING = 5;
  const PLAYING = 6;
  const PAUSED = 7;

  static function build($idx, $text, $style, $icon) {
    Status::$data[$idx] = [
      'text' => $text,
      'style' => $style,
      'icon' => $icon
    ];
  }

  static $data = [];

  static function to_array($s) {
    return Status::$data[$s];
  }
}

Status::build(Status::UNKNOWN,     'Unknown',     'default', 'question-sign');
Status::build(Status::ONLINE,      'Online',      'success', 'ok');
Status::build(Status::OFFLINE,     'Offline',     'warning', 'flash');
Status::build(Status::ERROR,       'Error',       'danger',  'remove');
Status::build(Status::UPLOADING,   'Uploading',   'success', 'open');
Status::build(Status::DOWNLOADING, 'Downloading', 'success', 'save');
Status::build(Status::PLAYING,     'Playing',     'success', 'play');
Status::build(Status::PAUSED,      'Paused',      'success', 'pause');
?>
