#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

npm i -g git+ssh://git@mangoweb.org:libs/mu-plugins.git || true
mup update || true
mup install || true

composer update
./vendor/bin/wp core update
./vendor/bin/wp plugin update --all
./vendor/bin/wp rewrite flush
