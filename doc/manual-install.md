
System requirements
------------------------
- PHP >= 7.1
- PostgreSQL or MySQL  
- Composer # See https://getcomposer.org/ 
    - install composer `curl -s https://getcomposer.org/installer | php`

Steps
------------------------

1. clone the repository: `git clone https://github.com/chrishodgson/recruit.git && cd recruit`
2. to use PostgreSQL, see `Using PostgreSQL instead of MySQL` below 
3. create the database - `bin/console doctrine:database:create`
4. install dependencies: `composer install`  
5. apply the schema - `bin/console doctrine:migrations:migrate`
6. load dummy data (optional) - `bin/console doctrine:fixtures:load`

Using PostgreSQL instead of MySQL 
------------------------
By default, the app is configured to used MySQL. To use PostgreSQL, follow these steps as point 2 above:
- `cp config/packages/doctrine_psql.yaml config/packages/doctrine.yaml` 
- `cp config/packages/doctrine_migrations_psql.yaml config/packages/doctrine_migrations.yaml`
- uncomment the contents of these yaml files 
- edit `DATABASE_URL` setting in `.env`

Run the standalone web server
------------------------
- run the stand alone web server: `bin/console server:run`

