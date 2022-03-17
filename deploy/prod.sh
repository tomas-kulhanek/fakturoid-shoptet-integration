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
./migrations \
./translations \
./vendor \
./queue \
./public \
--exclude=/queue/dev \
--exclude=/config/config.local.neon \
--exclude=/config/env/dev.neon \
tomaskul@tomaskulhanek.cz:/home4/tomaskul/fakturoid.tomaskulhanek.cz

./devstack exec php-fpm composer install
