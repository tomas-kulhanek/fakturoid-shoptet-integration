#!/usr/bin/env bash

echo "Deploy build $path to $server"
source .deploy.env

echo "Deploy Fakturoid API addon"
echo "============================="

# Read server directory path
if [ "$path" == '' ]; then
	read -p "Relative server directory path where to upload shop (without trailing slash): " path
	if [ "$path" == '' ]; then
		printf '%s\n' "Server directory path has to be set." >&2
		exit 1
	fi
fi

# Composer install for production?
read -p "Run \`composer install --no-dev\` before deploy: [y/N]:" install
if [ "$install" == '' ]; then
	install="n"
fi

# Composer install
if [ "$install" == 'Y' ]; then
	echo ""
	echo "Composer install"
	echo "---------------"
	composer install --no-dev
fi


# Build assets?
read -p "Run \`yarn encore prod\` before deploy: [y/N]:" build
if [ "$build" == '' ]; then
	build="n"
fi

# Build assets
if [ "$build" == 'Y' ]; then
	echo ""
	echo "Building assets"
	echo "---------------"
	yarn encore prod
fi

echo ""
echo "Deploying Fakturoid"
echo "----------------"

serverWithCredentials=$user@$server;

rsync -azP --delete --progress --filter='merge deploy-ignore.txt' "$localPath" "$serverWithCredentials":"$serverPath"

echo ""
echo "Clearing cache"
echo "--------------"
ssh "$serverWithCredentials" "mkdir -p $serverPath/var/temp/cache"
ssh "$serverWithCredentials" "mkdir -p $serverPath/var/log"
ssh "$serverWithCredentials" "cp  $productionPath/config/config.local.neon $serverPath/config"
ssh "$serverWithCredentials" "cd $serverPath && php bin/console nette:latte:warmup"
ssh "$serverWithCredentials" "cd $serverPath && php bin/console contributte:cache:generate"
ssh "$serverWithCredentials" "cd $serverPath && php bin/console orm:generate-proxies"


read -p "Link new build to production?: [y/N]:" build
if [ "$build" == '' ]; then
	build="Y"
fi

if [ "$build" == 'Y' ]; then
  ssh "$serverWithCredentials" "supervisorctl stop all"
  ssh "$serverWithCredentials" "cd $serverPath && php bin/console mig:co"
	ssh "$serverWithCredentials" "cd $serverPath && rm $productionPath"
  ssh "$serverWithCredentials" "cd $serverPath && chown -R www-data:www-data var"
  ssh "$serverWithCredentials" "ln -snf $serverPath $productionPath"
  ssh "$serverWithCredentials" "supervisorctl start all"
fi

ssh "$serverWithCredentials" "cd $serverPath && chown -R www-data:www-data var"


echo ""
echo "DONE"
