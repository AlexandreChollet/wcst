php composer.phar install

php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load --append

yarn encore production