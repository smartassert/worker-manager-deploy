#!/usr/bin/env bash

COMPOSE_FILES=$(ls ./docker-compose-config-source/*.yml)

echo "$COMPOSE_FILES"

COMMAND="docker-compose"

for FILE in $COMPOSE_FILES; do
  COMMAND="$COMMAND -f $FILE"
done

COMMAND="$COMMAND config --no-interpolate > docker-compose.yml"

echo "$COMMAND"
eval "$COMMAND"
