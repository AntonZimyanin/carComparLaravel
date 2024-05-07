sudo rm -rf storage/app/telegraph/TelegraphChat/ && \
redis-cli flushall && \
php artisan serve --port=8080