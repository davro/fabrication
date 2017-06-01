#!/bin/bash
#
# Run this script from the root directory
# This will run the code sniffer and unit tests
#
# $ bash bin/runall.bash
#
systemTestLog=$(pwd)/cache/system.tests.log

echo '' > $systemTestLog

echo '----------------------------------------------------------------------' >> $systemTestLog
echo 'CODE STYLE PSR2'  >> $systemTestLog
echo '----------------------------------------------------------------------' >> $systemTestLog
php ../bin/phpcs.phar --standard=PSR2 $(pwd)/library >> $systemTestLog

echo '----------------------------------------------------------------------' >> $systemTestLog
echo 'UNIT TESTS' >> $systemTestLog
echo '----------------------------------------------------------------------' >> $systemTestLog
#php ../bin/phpunit.phar --bootstrap $(pwd)/tests/bootstrap.php $(pwd)/tests/ >> $systemTestLog
php ../bin/phpunit.phar $(pwd)/tests/ >> $systemTestLog

cat $systemTestLog
