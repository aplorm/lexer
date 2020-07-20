#!/bin/bash
export XDEBUG_CONFIG="idekey=session_name remote_host=localhost remote_port=9999 profiler_enable=1"
echo "running phpunit"
vendor/bin/phpunit --coverage-html public/coverage

if [ $? -eq '0' ]; then
  echo "running behat"
  vendor/bin/behat
else
  echo "some unit test failed"
fi

