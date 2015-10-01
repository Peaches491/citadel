#! /bin/bash

echo 'Hello, World!'

end=$((SECONDS + 3600))

while [ $SECONDS -lt $end ]; do
  curl localhost/opcache_reset.php
  curl localhost
  sleep 15;
done
