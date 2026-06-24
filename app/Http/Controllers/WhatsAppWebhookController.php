<?php

namespace App\Http\Controllers;

use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\WhatsAppSession;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Jobs\ProcessOrderJob;
use App\Services\GeminiService;

class WhatsAppWebhookController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    // SEND MESSAGE

    private function sendMessage($to, $message)
    {
        $client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $client->messages->create(
            "whatsapp:$to",
            [
                'from' => config('services.twilio.from'),
                'body' => $message
            ]
        );
    }

    // CACHE HELPERS

    private function getCart($phone)
    {
        return Cache::get("cart:$phone", []);
    }

    private function saveCart($phone, array $cart)
    {
        Cache::put("cart:$phone", $cart, now()->addMinutes(30));
    }

    private function clearCart($phone)
    {
        Cache::forget("cart:$phone");
    }

    private function getSelection($phone)
    {
        return Cache::get("selection:$phone", []);
    }

    private function saveSelection($phone, array $selection)
    {
        Cache::put("selection:$phone", $selection, now()->addMinutes(30));
    }

    private function clearMemory($phone)
    {
        Cache::forget("cart:$phone");
        Cache::forget("selection:$phone");
    }

    // ENTRY

    public function handle(Request $request)
    {
        $phone = str_replace('whatsapp:', '', $request->input('From'));
        $message = trim(strtolower($request->input('Body')));

        $session = WhatsAppSession::firstOrCreate([
            'phone_number' => $phone
        ]);

        if (in_array($message, ['hi', 'hello'])) {
            return $this->showMainMenu($phone, $session);
        }

        // HANDLE "ADD MORE OR NOT" STEP

        if ($session->step === 'adding_products_confirm') {

            if ($message === 'yes') {
                $session->update(['step' => 'adding_products']);
                return $this->showProducts($phone);
            }

            if ($message === 'no') {
                $session->update(['step' => 'quantity_entry']);

                $selection = $this->getSelection($phone);

                $msg = "Great \n\nNow enter quantities for your selected items:\n\n";

                foreach ($selection as $item) {
                    $msg .= "{$item['product_id']}:1 for {$item['name']}\n";
                }

                $msg .= "\nFormat: Product Number:Quantity (e.g. 1:2,3:1)";

                $this->sendMessage($phone, $msg);

                return response()->json(['success' => true]);
            }
        }

        switch ($session->step) {

            case 'menu':
                return $this->handleMenuSelection($phone, $message, $session);

            case 'adding_products':
                return $this->handleProductSelection($phone, $message, $session);

            case 'quantity_entry':
                return $this->handleQuantityEntry($phone, $message, $session);

            case 'confirm':
                return $this->handleConfirmation($phone, $message, $session);
        }

        return response()->json(['success' => true]);
    }

    // MAIN MENU

    private function showMainMenu($phone, $session)
    {
        $session->update(['step' => 'menu']);

        $this->sendMessage(
            $phone,
            "Welcome to Nolo Market 🛒\n\n1. Start Order\n2. Checkout"
        );

        return response()->json(['success' => true]);
    }

    // MENU

    private function handleMenuSelection($phone, $message, $session)
    {
        if ($message == '1') {
            $session->update(['step' => 'adding_products']);
            return $this->showProducts($phone);
        }

        if ($message == '2') {
            return $this->showCartSummary($phone);
        }

        return response()->json(['success' => true]);
    }

    // SHOW PRODUCTS ONE BY ONE SELECTION

    private function showProducts($phone)
    {
        $products = Product::all();

        $msg = "Select a product by typing its ID:\n\n";

        foreach ($products as $p) {
            $msg .= "{$p->id}. {$p->name} - R{$p->price}\n";
        }

        $this->sendMessage($phone, $msg);

        return response()->json(['success' => true]);
    }

    // PRODUCT SELECTION (ONE AT A TIME)

    private function handleProductSelection($phone, $message, $session)
    {
        $product = Product::find($message);

        if (!$product) {
            $this->sendMessage($phone, "Invalid product. Please enter a valid product ID.");
            return response()->json(['success' => true]);
        }

        $selection = $this->getSelection($phone);

        $selection[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price
        ];

        $this->saveSelection($phone, $selection);

        $this->sendMessage(
            $phone,
            "{$product->name} added ✅\n\nWould you like to add something else? (YES/NO)"
        );

        $session->update(['step' => 'adding_products_confirm']);

        return response()->json(['success' => true]);
    }


    // QUANTITIES (FROM SELECTION)

    private function handleQuantityEntry($phone, $message, $session)
    {
        $selection = $this->getSelection($phone);

        if (empty($selection)) {
            $this->sendMessage($phone, "Session expired.");
            return response()->json(['success' => true]);
        }

        $qty = array_filter(array_map('trim', explode(',', $message)));

        $cart = [];

        foreach ($qty as $item) {

            [$id, $quantity] = array_map('trim', explode(':', $item));

            foreach ($selection as $sel) {
                if ($sel['product_id'] == $id) {

                    $cart[] = [
                        'product_id' => $sel['product_id'],
                        'name' => $sel['name'],
                        'price' => $sel['price'],
                        'quantity' => (int) $quantity
                    ];
                }
            }
        }

        $this->saveCart($phone, $cart);

        $session->update(['step' => 'confirm']);

        return $this->showCartSummary($phone);
    }

    // SUMMARY

    private function showCartSummary($phone)
    {
        $cart = $this->getCart($phone);

        $msg = "ORDER SUMMARY\n\n";
        $total = 0;

        foreach ($cart as $item) {
            $line = $item['price'] * $item['quantity'];
            $total += $line;

            $msg .= "{$item['name']} x {$item['quantity']} = R" .
                number_format($line, 2) . "\n";
        }

        $msg .= "\nTOTAL: R" . number_format($total, 2);
        $msg .= "\n\nReply YES to confirm or NO to cancel";

        $this->sendMessage($phone, $msg);

        return response()->json(['success' => true]);
    }

    // CONFIRM

    private function handleConfirmation($phone, $message, $session)
    {
        $message = strtoupper(trim($message));

        if ($message === 'NO') {
            $this->sendMessage($phone, "Order cancelled ❌");
            $this->clearCart($phone);
            $session->delete();
            return response()->json(['success' => true]);
        }

        if ($message !== 'YES') {
            $this->sendMessage($phone, "Reply YES or NO");
            return response()->json(['success' => true]);
        }

        $cart = $this->getCart($phone);

        $customer = Customer::firstOrCreate([
            'phone_number' => $phone
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'source' => 'whatsapp',
            'total' => 0
        ]);

        $total = 0;

        foreach ($cart as $item) {

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);

            $total += $item['price'] * $item['quantity'];
        }

        $order->update(['total' => $total]);


        $this->sendMessage(
            $phone,
            "ORDER CONFIRMED!\nOrder #{$order->id}\nTotal: R" . number_format($total, 2)
        );

        $this->clearMemory($phone);

        $session->update([
            'step' => null,
        ]);

        $session->delete();

        return response()->json(['success' => true]);
    }
}