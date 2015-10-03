#! /bin/bash

echo 'Hello, World!'

end=$((SECONDS + 60))

while [ $SECONDS -lt $end ]; do
  curl localhost/?forceClearCache
  sleep 15;
done
