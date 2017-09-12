#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

cp -n ./config/config.local.sample.neon ./config/config.local.neon || true
php ./bin/fill-salts.php
