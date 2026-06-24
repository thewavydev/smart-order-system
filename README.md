# Smart Order System

This is a full-stack ordering system that enables customers to place orders via WhatsApp using a simple menu flow. It combines Laravel, Vue.js 3, and MySQL.


## What it does

- Shows a dashboard at `/`.
- Shows orders at `/orders`.
- Creates orders via API:
  - `POST /api/orders/store` (uses `OrderController@store`).
- Accepts WhatsApp webhook messages at `POST /webhooks/whatsapp` (uses `WhatsAppWebhookController@handle`).
  - Simple menu flow: show menu → show products → collect quantities → confirm.


## How to run

### Prerequisites

- PHP 8.3+
- Composer
- Node.js (for the Vite/Vue frontend)
- A database (MySQL)

### 1) Local (no Docker)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

php artisan migrate --seed

php artisan serve
npm run dev
```

Then open:
- http://127.0.0.1:8000

### 2) Docker (MySQL + Mailpit)

```bash
docker-compose up --build
```

Docker will run:
- MySQL (database)
- Mailpit (email testing at http://localhost:8025)


## Key routes

- `GET /` → dashboard view
- `GET /orders` → orders view
- `POST /webhooks/whatsapp` → WhatsApp webhook handler
- `GET /api/orders/index` → list orders (paginated)
- `POST /api/orders/store` → create order

## WhatsApp order flow (quick)

1. Send `hi` or `hello`
2. Reply `1` to start order (shows products with IDs)
3. Send a product ID to add it
4. Send quantities in this format:
   - `ProductNumber:Quantity` (example: `1:2,3:1`)
5. Confirm with `YES` (or cancel with `NO`)

## Notes

- WhatsApp webhook requires Twilio credentials (`services.twilio.*`).
- Gemini ordering requires `GEMINI_API_KEY`.



