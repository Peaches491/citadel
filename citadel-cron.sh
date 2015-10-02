#! /bin/bash

echo 'Hello, World!'

end=$((SECONDS + 60))

while [ $SECONDS -lt $end ]; do
  curl localhost/?forceClearCache
  curl localhost
  sleep 5;
done
