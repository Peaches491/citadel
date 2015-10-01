<?php
var_dump("OPCache reset: " . opcache_reset());

$mcd = new Memcached('mc');
if (!count($mcd->getServerList())) {
  $mcd->addServer("localhost", 0);
  $mcd->setOption( Memcached::OPT_LIBKETAMA_COMPATIBLE, true );
}
var_dump("Memcached reset: " . $mcd->flush());
?>
