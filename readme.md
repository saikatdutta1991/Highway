## Configure .env file

## Build docker services and run:
```
docker-compose up --build
```

## Migrate tables
```
docker exec -it app bash
php artisan migrate
```

## Create admin
```
php artisan create-admin
```

## Seed vehicle services like auto, prime, etc.
```
php artisan db:seed --class=ServiceSeeder
** Don't call this multiple times. There is no log right now.
```