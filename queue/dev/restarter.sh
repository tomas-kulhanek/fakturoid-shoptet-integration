#!/bin/sh
source ~/.bash_profile
source /home4/tomaskul/domains/dev-fakturoid.tomaskulhanek.cz/queue/dev/config.sh

PID=$(cat $QUEUE_VAR_DIR/worker1.pid)

if ps -p $PID >/dev/null; then
  echo "$PID is running"
  # Do something knowing the pid exists, i.e. the process with $PID is running
else
  $QUEUE_DIR/dev/worker.sh restart
fi
