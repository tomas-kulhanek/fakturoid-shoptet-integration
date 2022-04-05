#Remove all dev dependencies
./devstack exec php-fpm composer install --no-dev

#Prepare JS/CSS
yarn encore prod

rsync \
-av \
--delete \
./src \
./config \
./bin \
./var \
./migrations \
./translations \
./vendor \
./queue \
./public \
--exclude=/queue/dev \
--exclude=/config/config.local.neon \
--exclude=/config/env/dev.neon \
--exclude=/var/**.{html,log,php,lock} \
tomaskul@tomaskulhanek.cz:/home4/tomaskul/domains/fakturoid.tomaskulhanek.cz

./devstack exec php-fpm composer install
