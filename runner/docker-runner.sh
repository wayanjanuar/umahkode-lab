#!/usr/bin/env bash
# runner/docker-runner.sh
# Example runner — HARDEN BEFORE PRODUCTION.
# usage: ./docker-runner.sh /absolute/path/to/workdir

WORKDIR="$1"
if [ -z "$WORKDIR" ]; then echo "Usage: $0 /path"; exit 1; fi

docker run --rm   --network none   --pids-limit 64   --memory 256m --cpus 0.5   -v "$WORKDIR":/app   -w /app   php:8.1-cli:latest   timeout 5 php app.php
