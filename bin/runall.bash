#!/bin/bash
#
# Run this script from the root directory
# This will run the code sniffer and unit tests
#
# $ bash bin/runall.bash
#

php vendor/bin/phpcs --standard=PSR2 $(pwd)/library

echo '----------------------------------------------------------------------'
php vendor/bin/phpunit $(pwd)/tests/

