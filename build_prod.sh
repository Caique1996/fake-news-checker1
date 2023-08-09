cd sistema
sudo chown -R www-data:www-data sistema/storage
sudo chown -R www-data:www-data sistema/storage
git pull
php composer.phar update
php artisan migrate
php artisan cache:clear
php artisan config:clear
php artisan view:clear
npm i
npm run build:all
