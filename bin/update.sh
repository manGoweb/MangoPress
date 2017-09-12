#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

composer update
./vendor/bin/wp core update
./vendor/bin/wp plugin update --all
./vendor/bin/wp rewrite flush
