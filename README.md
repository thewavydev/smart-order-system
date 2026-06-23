# Smart Order System

This is a full-stack SaaS-style ordering system that enables customers to place orders via WhatsApp using natural language or structured menu flows. It combines Laravel 13, Vue.js 3, MySQL, and Gemini AI to provide an intelligent, conversational commerce experience for small to medium businesses.

## What it does

- Shows a basic dashboard at `/`.
- Shows orders at `/orders`.
- Creates orders via API:
  - `POST /api/orders/store` (uses `OrderController@store`)
- Accepts WhatsApp webhook messages at `POST /webhooks/whatsapp` (uses `WhatsAppWebhookController@handle`).
  - Supports a menu flow (view products → place order → confirm).
  - Can also parse an order intent using Gemini (see `GeminiService`).
  - When an order is created, it dispatches queued jobs (email/order processing).

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

### 2) Docker (uses MySQL + RabbitMQ + Mailpit)

```bash
docker-compose up --build
```

Docker will run:
- MySQL (database)
- RabbitMQ (queue)
- Mailpit (email testing)
- A Laravel queue worker container (`php artisan queue:work ...`)

## Key routes

- `GET /` → dashboard view
- `GET /orders` → orders view
- `POST /webhooks/whatsapp` → WhatsApp webhook handler
- `GET /api/orders/index` → list orders (paginated)
- `POST /api/orders/store` → create order

## Notes

- WhatsApp webhook requires Twilio credentials (`services.twilio.*`).
- Gemini ordering requires `GEMINI_API_KEY`.
- Queue worker is required for queued jobs (email sending).

