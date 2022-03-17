#!/bin/sh
source ~/.bash_profile
source /home4/tomaskul/dev-fakturoid.tomaskulhanek.cz/queue/dev/config.sh
# CMD is the actual command that runs the service (e.g. PHP)


CMD="$PHP_EXEC $BASE_DIR/bin/console messenger:consume rabbitmq"
SERVICE_NAME=worker1
PID_FILE_DIR=$QUEUE_VAR_DIR
PID_FILE=$PID_FILE_DIR/$SERVICE_NAME.pid

LOGFILE=$LOGDIR/$SERVICE_NAME.log
LOGFILEDATE=$(date '+%F:%T')

USAGE_TEXT="Usage: $0 start|stop|restart|status"

start_service() {
  if [ ! -f $PID_FILE ]; then
    echo -n "Starting $SERVICE_NAME... "
    #PID=`${PreCmd} "nohup $CMD >> ${LOGFILE} 2>&1 </dev/null & echo \\\$! "`
    #echo $PID > $PID_FILE
    #nohup $CMD >/dev/null 2>&1 &
    nohup $CMD > /dev/null 2>&1 & echo $! > $PID_FILE
    chown $USER:$USER $PID_FILE
    echo "Started!"
  else
    echo "$SERVICE_NAME is already running. PID file exists."
  fi
}

stop_service() {
  status_service do_stop
}

status_service() {
  SERVICE_STATUS=""
  if [ -f $PID_FILE ]; then
    PID=$(cat $PID_FILE)
    if [ "$PID" = "" ]; then
      echo "$SERVICE_NAME is in an unknown state. Please restart. "
    else
      is_running=$(ps -e | grep $PID | wc -l)
      if [ "$is_running" = "1" ]; then
        if [ "$1" = "" ]; then
          # this is just status
          echo "$SERVICE_NAME is running"
        else
          if [ "$1" = "do_stop" ]; then
            echo -n "$SERVICE_NAME stopping... "
            kill -9 "$PID"
            echo "Stopped!"
            rm $PID_FILE
          fi
        fi
      else
        rm $PID_FILE
        echo "$SERVICE_NAME is not running. PID file cleaned up."
      fi
    fi
  else
    echo "$SERVICE_NAME is not running."
  fi
}

[ "$1" = "" ] && {
  echo $USAGE_TEXT
  echo ""
  exit 1
}

[ -d ${PID_FILE_DIR} ] || {
  mkdir -p $PID_FILE_DIR
  chown $USER:$USER $PID_FILE_DIR
}

case $1 in
start)
  cd ${SERVICE_DIR}
  start_service && sleep 1
  ;;
stop)
  stop_service
  ;;
restart)
  stop_service
  sleep 2
  start_service && sleep 1
  ;;
status)
  status_service
  ;;
esac
