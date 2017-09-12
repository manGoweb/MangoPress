#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

composer install
mango install || true

./vendor/bin/wp core download || true
./bin/clone-local-config.sh

mup update || true
mup install || true

echo ""
echo "Dont forget to fill in your config.local.neon."
echo "You might want to delete /vendor and /public from .gitignore."
echo "Happy coding!"
echo ""
