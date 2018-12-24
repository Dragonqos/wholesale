### For docker

Install Docker onto your machine: 
https://docs.docker.com/install/


Open console and navigate to project dir:

```
cp .env.docker.dist .env
docker-compose up -d
sh docker/start.sh
```
_Service will available on localhost:8002_


# bash commands
```
$ docker-compose exec php bash
```

# Composer (e.g. composer update)
```
$ docker-compose exec php composer update
```

# SF commands 
```
$ docker-compose exec php php /var/www/wholesale/bin/console cache:clear
```

# Same command by using alias
```
$ docker-compose exec php bash
$ sf cache:clear
```