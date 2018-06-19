
System requirements
------------------------
- PHP >= 7.1
- PostgreSQL or MySQL  
- Composer # See https://getcomposer.org/ 
    - install composer `curl -s https://getcomposer.org/installer | php`

Steps
------------------------

1. clone the repository: `git clone https://github.com/chrishodgson/recruit.git && cd recruit`
2. edit `DATABASE_URL` setting in `.env`
3. create the database - `bin/console doctrine:database:create`
4. install dependencies: `composer install`  
5. apply the schema - `bin/console doctrine:migrations:migrate`
6. load dummy data (pptional) - `bin/console doctrine:fixtures:load`

Using PostgreSQL instead of MySQL 
------------------------
By default, the app is configured to used MySQL. To use PostgreSQL, edit files as part of step 2 above :
- `config/packages/doctrine.yaml` 
- `config/packages/doctrine_migrations.yaml`

Run the standalone web server
------------------------

- run the stand alone web server: `bin/console server:run`

