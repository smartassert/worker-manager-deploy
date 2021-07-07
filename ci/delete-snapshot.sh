#!/usr/bin/env bash

EXIT_CODE_RESPONSE_SNAPSHOT_MISSING=1
EXIT_CODE_SNAPSHOT_ID_MISSING=2
EXIT_CODE_SNAPSHOT_ID_INCORRECT=3

AUTH_HEADER="Authorization: Bearer ${DIGIALOCEAN_API_TOKEN}"
URL="https://api.digitalocean.com/v2/snapshots/${ID}"

curl -s -X DELETE -H 'Content-Type: application/json' -H "${AUTH_HEADER}" "${URL}"
