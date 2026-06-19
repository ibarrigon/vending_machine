#!/bin/bash
set -e

chmod 644 /etc/mysql/conf.d/my.cnf

exec docker-entrypoint.sh "$@"
