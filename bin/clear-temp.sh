#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'

./vendor/bin/wp transient delete --all
