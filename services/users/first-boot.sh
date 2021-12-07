#!/usr/bin/env bash

{
  echo "DATABASE_URL=$DATABASE_URL"
  echo "JWT_PASSPHRASE=$JWT_PASSPHRASE"
  echo "PRIMARY_ADMIN_TOKEN=$PRIMARY_ADMIN_TOKEN"
  echo "SECONDARY_ADMIN_TOKEN=$SECONDARY_ADMIN_TOKEN"
  echo "IS_READY=$IS_READY"
} >> /etc/environment

base64 -d <<< "$JWT_SECRET_KEY_BASE64_PART1$JWT_SECRET_KEY_BASE64_PART2$JWT_SECRET_KEY_BASE64_PART3" > jwt/private.pem
base64 -d <<< "$JWT_PUBLIC_KEY_BASE64" > jwt/public.pem

PUBLIC_IP=$(dig @resolver4.opendns.com myip.opendns.com +short)
VERSION="$VERSION" \
CADDY_IP="$PUBLIC_IP" \
DATABASE_URL="$DATABASE_URL" \
JWT_PASSPHRASE="$JWT_PASSPHRASE" \
PRIMARY_ADMIN_TOKEN="$PRIMARY_ADMIN_TOKEN" \
SECONDARY_ADMIN_TOKEN="$SECONDARY_ADMIN_TOKEN" \
IS_READY="$IS_READY" \
docker-compose up -d

sudo docker-compose exec -T app php bin/console doctrine:database:create --if-not-exists
sudo docker-compose exec -T app php bin/console doctrine:migrations:migrate --no-interaction
