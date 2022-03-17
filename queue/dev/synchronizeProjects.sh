#!/bin/sh
source ~/.bash_profile
source /home4/tomaskul/dev-fakturoid.tomaskulhanek.cz/queue/dev/config.sh

LOGFILEDATE=synchronize_$(date '+%F')
LOGFILE=$LOGDIR/$LOGFILEDATE.log

$PHP_EXEC $BASE_DIR/bin/console shoptet:synchronize:projects >> $LOGFILE
