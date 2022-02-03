#!/usr/bin/env bash

echo "--------------------------"
echo "---- Dokladomat scheduler ---"
echo "--------------------------"
echo ""
echo "Delay between jobs is 4 hours."

while true
do
  now=$(date +"%d.%m. %Y %I:%M:%S")
  echo "[$now] Running scheduler"
  php bin/console shoptet:synchronize:projects
  sleep 3600
done
