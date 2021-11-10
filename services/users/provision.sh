#!/usr/bin/env bash

mkdir -p /var/log
chown -R www-data:www-data /var/log

sudo VERSION="$VERSION" docker-compose -f docker-compose.yml -f docker-compose-caddy.yml up -d
