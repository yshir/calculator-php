#!/bin/sh

docker run -it --rm \
  --name calculator-php \
  -v "$PWD/src":/usr/src/myapp \
  -w /usr/src/myapp php:7.4-cli php repl.php
