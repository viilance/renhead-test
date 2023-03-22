## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.4/installation#installation)

Clone the repository 

    git clone https://github.com/viilance/batterium-test.git

Switch to the repo folder

    cd batterium-test

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Make sure you have the [Docker](https://www.docker.com) up and running, and have the required database driver installed

    sudo apt-get install php-mysql

Start the docker container by running the laravel sail command

    ./vendor/bin/sail up -d

Generate the application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Run the database seeder

    php artisan db:seed

You can now access the application at http://localhost

**TL;DR command list**

    git clone https://github.com/viilance/batterium-test.git
    cd batterium-test
    composer install
    cp .env.example .env
    sudo apt-get install php-mysql
    ./vendor/bin/sail up -d
    php artisan key:generate

**Make sure you set the correct database connection information before running the migrations**

    php artisan migrate
    php artisan db:seed

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh

The api can be accessed at [http://localhost/api](http://localhost/api).

## API Specification

The api spec can be found inside the docs folder in the project root

----------
