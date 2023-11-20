# eventponit.app

## Setup

1. run `docker compose up -d`
2. run `bin/console doctrine:fixtures:load` (yes, when asked if you want to purge the database)

## Adminer

in order to access the inbuilt table GUI, we are using [Adminer](https://www.adminer.org/) to access it:

1. open `http://localhost:8000/` and login (system: PostgreSQL server: database username: root,
   password; eventpoint, database: eventpoint)
