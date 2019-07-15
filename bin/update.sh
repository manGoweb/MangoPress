#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

yarn upgrade-interactive

composer update

npm i -g git+ssh://git@mangoweb.org:libs/mu-plugins.git || true
mup update || true
mup install || true

vendor/bin/wp plugin deactivate disable-wordpress-updates
vendor/bin/wp core update
vendor/bin/wp plugin update --all
