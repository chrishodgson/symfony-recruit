
Install dependencies
------------------------

# note pagination bundle - requires translator
`composer require doctrine twig validator symfony/security-csrf validator translator knplabs/knp-paginator-bundle`
`composer require doctrine/doctrine-migrations-bundle "^1.0" #doctrine migrations`
# debug includes phpunit bridge, profiler, monolog and dump
`composer require debug orm-fixtures fzaninotto/Faker --dev`


Symfony config changes
------------------------

- enable boostrap in `twig.yaml` and `pagination.yaml`
- enable csrf in `framework.yaml`
- enable validation in `framework.yaml`

Useful commands
------------------------

- create the initial project - `composer create-project symfony/skeleton recruit` 
- create the db (first edit connection in .env) - `bin/console doctrine:database:create`
- load dummy data `bin/console doctrine:fixtures:load`
- run standalone webserver  - `bin/console server:run`
- see list of routes - `bin/console debug:router` 
- create a migration - `bin/console make:migration`
- apply a migration - `bin/console doctrine:migrations:migrate`

Optional packages
------------------------

# maker - `composer require maker`
- example adding an entity - `bin/console make:entity Company`
- example adding a controller - `bin/console make:controller ActivityController`
- example adding a controller (alternative syntax) - `bin/console make:crud Activity`

# security checker (checks packages installed) - `composer require sec-checker && bin/console security:check`
 


