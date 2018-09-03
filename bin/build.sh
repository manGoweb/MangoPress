#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

npm install
npm run build
