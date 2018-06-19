
Description
------------------------
Recruit is a simple web application built with Symfony 4 and bootstrap 4 to keep track of any communications 
with recruiters. It has been built with contractors in mind who are regularly contacted by recruitment agents and 
offers a central place to keep a record of phone calls, voicemails, emails or linkedin messages. It consists of 
Contacts each of which have Activities associated. 

Docker install
------------------------

Recommended way to install is via docker...

- install docker # See https://docs.docker.com/ 
- clone the repository: `git clone https://github.com/chrishodgson/recruit.git && cd recruit`
- build and start the docker containers - `cd docker/lamp-stack && docker-compose build && docker-compose up -d`
- install dependencies - `docker-compose exec php composer install`
- apply the schema - `docker-compose exec php bin/console doctrine:migrations:migrate`
- load dummy data (optional step) - `docker-compose exec php bin/console doctrine:fixtures:load`
- load in browser - `open localhost:8001`

Manual install
------------------------

see docs/manual-install.md

Heroku install
------------------------

see docs/heroku.md
