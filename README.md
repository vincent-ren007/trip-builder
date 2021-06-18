## Trip Builder

- [Online demo](https://trip-builder.learn-naturally.com)
- [API documentation](https://trip-builder.learn-naturally.com/docs)


## Installation

> requirements: be sure you have PHP(7.4 or higher), Composer, git, Docker and docker-compose installed in your machine.

```bash
git clone https://github.com/www-vincent-ren/trip-builder.git
cd trip-builder
composer install
cp .env.example .env
php artisan key:generate
```
* Add following configuration in .env. 
```bash
APP_PORT=8007
FORWARD_DB_PORT=3356
```

* Edit following configuration in .env.
```bash
APP_URL=http://localhost:8007
DB_HOST=mysql
DB_DATABASE=trip_builder
DB_USERNAME=sail
DB_PASSWORD=password
```

* Build development environment.
```bash
./vendor/bin/sail up 		# It may take a few minutes.
```

* Migrate and seed database after Mysql is ready.
```bash
./vendor/bin/sail php artisan migrate --seed
```

* Testing.
```bash
./vendor/bin/sail php artisan test --testsuite=Feature
```

* Done, now you should be able to access [http://localhost:8007](http://localhost:8007)
