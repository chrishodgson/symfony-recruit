
install steps
------------------------
 
- Install Heroku CLI (OSX) `https://cli-assets.heroku.com/branches/stable/heroku-osx.pkg`
- Create a Heroku app (you will need an account) - `heroku create`
- Log into Heroku - `heroku login`
- Add PHP buildpack - `heroku buildpacks:set heroku/php`
- Add postgreSQL addon - `heroku addons:create heroku-postgresql:hobby-dev`
- Create symfony environment variables (change YOUR SECRET)- `heroku config:set APP_ENV=prod APP_SECRET=YOUR SECRET`
- Follow steps for `Using PostgreSQL instead of MySQL` in `manual-instal.md`
- Push the changes to Heroku - `git push heroku master`
- Open the app on Heroku - `heroku open`

useful commands
------------------------

- tail the logs - `heroku logs --tail`
- run an interactive bask shell - `heroku run bash`
