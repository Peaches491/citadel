<?php

class Cachable {
  static $mc;
  var $key = "";
  var $hash_source = "";
  var $hash = 0;
  var $expire = 0;
  var $target_func = 0;
  var $debug = false;

  function __construct($key, $expire, $hash_source = 0, $target_func = 0) {
    $this->expire = $expire;
    $this->target_func = $target_func;
    $this->key = $key;

    if( $hash_source ) {
      $this->hash_source = $hash_source;
      $this->hash = crc32(file_get_contents($this->hash_source));
    }
  }

  function get() {
    $result = Cachable::$mc->get($this->key);

    if( $result == NULL ) {
      if($this->debug) var_dump('Result Null! ' . $this->key);
      return $this->update_cache();
    } else {
      if( $this->hash_source ) {
        if( is_array($result) && 
            array_key_exists('hash', $result) &&
            array_key_exists('value', $result) ) {
          if( $result['hash'] == $this->hash ) {
            if($this->debug) var_dump('Hash match! ' . $this->key . ' -> ' . $this->hash);
            return $result['value'];
          } else {
            if($this->debug) var_dump('Hash mismatch! ' . $this->key);
          }
        }
        if($this->debug) var_dump('Hash exits. Return malformatted! ' . $this->key);
        return $this->update_cache();
      } else {
        if($this->debug) var_dump('No hash, returning');
        return $result;
      }
    }
    return $result;
  }

  function set($value) {
    if( $this->hash_source ) {
      $value = [
        'hash' => $this->hash,
        'value' => $value
      ];
    }
    Cachable::$mc->set($this->key, $value, $this->expire);
  }

  function update_cache() {
    if($this->debug) var_dump('Update Cache! ' . $this->key);
    if( $this->target_func ) {
      $data = call_user_func($this->target_func);
      $this->set($data);
      return $data;
    }
  }
}
Cachable::$mc = new Memcached('mc');
Cachable::$mc->addServer('localhost', 0);

?>
