
Required steps
------------------------

- create the database - `bin/console doctrine:database:create`
- apply the schema ( doctrine migration ) - `bin/console doctrine:migrations:migrate`

Optional steps
------------------------

- load some dummy data - `bin/console doctrine:fixtures:load`