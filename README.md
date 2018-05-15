### Description
Recruit is a simple web application built with Symfony 4 and bootstrap 4 to keep track of any communications 
with recruiters. It has been built with contractors in mind who are regularly contacted by recruitment agents and 
offers a central place to keep a record of anhy phone calls, voicemails, emails or linkedin messages. It consists of 
Contacts each of which have Activities associated. 

### System requirements
- PHP >=7.1
- MySQL (or other supported database) # http://www.doctrine-project.org/2010/02/11/database-support-doctrine2.html 
- composer # See https://getcomposer.org/ for more information and documentation.

### Installation 
- clone the repository: `git clone https://github.com/chrishodgson/recruit.git` 
- cd into the `recruit` folder                     
- install dependencies: `composer install`  
- edit `DATABASE_URL` in `.env`
- create the database: `bin/console doctrine:database:create`  
- apply the database schema: `bin/console doctrine:migrations:migrate`  
- load some dummy data (optional): `bin/console doctrine:fixtures:load`  
- run the stand alone web server: `bin/console server:run` 