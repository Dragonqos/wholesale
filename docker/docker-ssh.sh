#!/bin/bash -xe

# docker container id or name might be given as a parameter
CONTAINER=$1

if [[ "$CONTAINER" == "" ]]; then
  # if no id given simply just connect to the first running container
  CONTAINER=$(docker ps | grep -Eo "^[0-9a-z]{8,}\b")
fi

# start an interactive bash inside the container
# note some containers don't have bash, then try: ash (alpine), or simply sh
# the -l at the end stands for login shell that reads profile files (read man)
docker exec -i -t $CONTAINER bash -l