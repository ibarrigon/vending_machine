[< Go back](../README.md)

# How to start

## Docker

''''
docker compose up -d --build

docker compose exec app composer install

docker compose exec app php bin/console doctrine:migrations:migrate

docker compose exec app php bin/phpunit
''''