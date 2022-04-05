#!/bin/sh
source ~/.bash_profile
source /home4/tomaskul/domains/fakturoid.tomaskulhanek.cz/queue/prod/config.sh

LOGFILEDATE=synchronize_$(date '+%F')
LOGFILE=$LOGDIR/$LOGFILEDATE.log

$PHP_EXEC $BASE_DIR/bin/console shoptet:synchronize:projects >> $LOGFILE
