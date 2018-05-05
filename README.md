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