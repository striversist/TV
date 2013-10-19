#!/bin/sh

# HTML header, using utf-8 encoding
TV_ROOT=/var/www/projects/TV
CRON_PATH=$TV_ROOT/cron
OUTPUT_FILENAME=hot_result.html

echo "<!DOCTYPE html><html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body>" > $CRON_PATH/$OUTPUT_FILENAME

# Dump data
#export USE_ZEND_ALLOC=0
`export USE_ZEND_ALLOC=0; php $TV_ROOT/app/start_collect_hot.php >> $CRON_PATH/$OUTPUT_FILENAME`

# HTML end
echo "</body></html>" >> $CRON_PATH/$OUTPUT_FILENAME


