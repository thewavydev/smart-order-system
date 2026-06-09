*** End Patch
#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "-- Docker compose status --"
docker-compose ps || true

echo "\n-- Service ports on host (common) --"
if command -v ss >/dev/null 2>&1; then
  ss -lntp | grep -E '1025|5672|3306' || true
else
  netstat -lntp 2>/dev/null | grep -E '1025|5672|3306' || true
fi

echo "\n-- Tail docker logs (rabbitmq, mailpit, queue_worker) --"
echo "Press Ctrl+C to stop the tail"
docker-compose logs --tail=200 -f rabbitmq mailpit queue_worker &
TAIL_PID=$!

# In parallel, search the Laravel log for common errors
sleep 2
echo "\n-- Recent AMQP / SMTP errors from storage/logs/laravel.log --"
if [ -f storage/logs/laravel.log ]; then
  grep -E "(Unable to connect to tcp://127.0.0.1:5672|stream_socket_client|SMTP|Connection refused)" storage/logs/laravel.log | tail -n 200 || true
else
  echo "No storage/logs/laravel.log found"
fi

# Show failed jobs in the queue service (if running)
echo "\n-- Failed jobs (php artisan queue:failed) --"
if docker-compose ps | grep -q "queue_worker"; then
  docker-compose exec -T queue_worker php artisan queue:failed || true
else
  echo "queue_worker service not running; skipping artisan queue:failed"
fi

# Wait for the logs tail to end (user interrupts)
wait $TAIL_PID || true
