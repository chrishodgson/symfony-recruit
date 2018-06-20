
Description
------------------------
Recruit is a simple web application built using Symfony 4 and bootstrap 4 for job hunters to keep track of communications 
with recruiters. It has been built with contractors in mind who are in regular contact with recruitment agents and 
offers a central place to record phone calls, voicemails, emails or linkedin messages. It consists of 
Contacts each of which have Activities associated. 

Docker install
------------------------

Recommended way to install is via docker...

- install docker # See https://docs.docker.com/ 
- clone the repository: `git clone https://github.com/chrishodgson/recruit.git && cd recruit/docker/apache-mysql`
- optional: by default it uses apache + mysql, to use nginx + postgresql, see `Using PostgreSQL + Nginx` below 
- build and start the docker containers - `docker-compose build && docker-compose up -d`
- install dependencies via composer - `docker-compose exec php composer install`
- apply the schema via doctrine - `docker-compose exec php bin/console doctrine:migrations:migrate`
- optional: load dummy data - `docker-compose exec php bin/console doctrine:fixtures:load`
- load app in web browser - `open localhost:8001`

Using PostgreSQL + Nginx
------------------------
`cd recruit/docker/nginx-postgress`
see `Using PostgreSQL instead of MySQL` in docs/manual-install.md

Manual install
------------------------
see docs/manual-install.md

Heroku install
------------------------
see docs/heroku.md
