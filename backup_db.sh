#!/bin/bash
# backup_db.sh - Daily MySQL backup script for Mini-Netumo
# Usage: ./backup_db.sh

DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_DIR="/var/backups/netumo"
DB_CONTAINER="$(docker-compose ps -q db)"
DB_USER="netumo"
DB_PASS="netumo"
DB_NAME="netumo"

mkdir -p $BACKUP_DIR

docker exec $DB_CONTAINER mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/netumo_backup_$DATE.sql

# Optional: keep only last 7 backups
tmp=$(ls -1t $BACKUP_DIR/netumo_backup_*.sql | tail -n +8)
if [ -n "$tmp" ]; then
  echo "$tmp" | xargs rm -f
fi

echo "Backup complete: $BACKUP_DIR/netumo_backup_$DATE.sql"
