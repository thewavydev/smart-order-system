<?php

namespace App\Http\Controllers;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
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

    public function handle(Request $request)
    {
        $phone = str_replace(
            'whatsapp:',
            '',
            $request->input('From')
        );

        $message = trim(
            strtolower(
                $request->input('Body')
            )
        );

        $session = WhatsAppSession::firstOrCreate(['phone_number' => $phone]);

         //AI Ordering
       
        if ($session->step !== 'ai_confirm' && !in_array($message,['hi', 'hello']))
        {
            $aiOrder = $this->gemini->parseOrder($message);
            if (
                $aiOrder &&
                isset($aiOrder['intent']) &&
                $aiOrder['intent'] === 'order'
            ) {
                return $this->showAIOrderSummary(
                    $phone,
                    $aiOrder,
                    $session
                );
            }
        }
        
        // Menu Flow
      
        if (in_array($message,['hi', 'hello'])) 
        {
            return $this->showMainMenu($phone,$session);
        }

        switch ($session->step) {

            case 'menu':
                return $this->handleMenuSelection(
                    $phone,
                    $message,
                    $session
                );

            case 'select_product':
                return $this->handleProductSelection(
                    $phone,
                    $message,
                    $session
                );

            case 'quantity':
                return $this->handleQuantitySelection(
                    $phone,
                    $message,
                    $session
                );

            case 'confirm':
                return $this->handleConfirmation(
                    $phone,
                    $message,
                    $session
                );
        }

        return response()->json([
            'success' => true
        ]);
    }

    private function showMainMenu( $phone, $session)
    {
        $session->update([
            'step' => 'menu'
        ]);

        $this->sendMessage(
            $phone,
            "Welcome to QueueFlow\n\n".
            "1. View Products\n".
            "2. Place Order\n".
            "3. Check Order Status"
        );

        return response()->json([
            'success' => true
        ]);
    }

    private function handleAIOrder(string $phone,array $aiOrder,WhatsAppSession $session)
    {
        $customer = Customer::firstOrCreate([
            'phone_number' => $phone
        ]);

        $total = 0;

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'source' => 'whatsapp_ai',
            'total' => 0
        ]);

        foreach ($aiOrder['items'] as $item) {

            $product = Product::where(
                'name',
                'like',
                '%' . $item['product'] . '%'
            )->first();

            if (!$product) {
                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price
            ]);

            $total += (
                $product->price *
                $item['quantity']
            );
        }

        $order->update([
            'total' => $total
        ]);

        ProcessOrderJob::dispatch(
            $order
        )->onQueue('orders');

        $this->sendMessage(
            $phone,
            "Order #{$order->id} created successfully.\n".
            "Total: R{$total}"
        );

        $session->delete();

        return response()->json([
            'success' => true
        ]);
    }

    private function showAIOrderSummary(string $phone,array $aiOrder,WhatsAppSession $session)
    {
        $summary = "Order Summary\n\n";
        $total = 0;

        foreach ($aiOrder['items'] as $index => $item) {

            $product = Product::whereRaw(
                'LOWER(name) LIKE ?',
                ['%' . strtolower($item['product']) . '%']
            )->first();

            if (!$product) {
                continue;
            }

            $lineTotal =
                $product->price *
                $item['quantity'];

            $total += $lineTotal;

            $summary .=
                ($index + 1) .
                ". {$product->name} x {$item['quantity']} = R" .
                number_format($lineTotal, 2) .
                "\n";
        }

        $summary .=
            "\nTotal: R" .
            number_format($total, 2) .
            "\n\nReply YES to confirm or NO to cancel.";

        $session->update([
            'step' => 'ai_confirm',
            'pending_order' => json_encode($aiOrder)
        ]);

        $this->sendMessage(
            $phone,
            $summary
        );

        return response()->json([
            'success' => true
        ]);
    }

    private function handleAiConfirmation(string $phone,string $message,WhatsAppSession $session)
    {
        if (strtoupper($message) === 'NO') {

            $this->sendMessage(
                $phone,
                'Order cancelled.'
            );

            $session->delete();

            return response()->json([
                'success' => true
            ]);
        }

        if (strtoupper($message) !== 'YES') {

            $this->sendMessage(
                $phone,
                'Reply YES to confirm or NO to cancel.'
            );

            return response()->json([
                'success' => true
            ]);
        }

        $orderData = json_decode(
            $session->pending_order,
            true
        );

        $customer = Customer::firstOrCreate([
            'phone_number' => $phone
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'source' => 'whatsapp_ai',
            'total' => 0
        ]);

        $total = 0;

        foreach ($orderData['items'] as $item) {

            $product = Product::whereRaw(
                'LOWER(name) LIKE ?',
                ['%' . strtolower($item['product']) . '%']
            )->first();

            if (!$product) {
                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => (int) $item['quantity'],
                'price' => $product->price
            ]);

            $total +=
                $product->price *
                (int) $item['quantity'];
        }

        $order->update([
            'total' => $total
        ]);

        ProcessOrderJob::dispatch(
            $order
        )->onQueue('orders');

        $this->sendMessage(
            $phone,
            "Order #{$order->id} created successfully.\n" .
            "Total: R" . number_format($total, 2) .
            "\nStatus: Pending"
        );

        $session->delete();

        return response()->json([
            'success' => true
        ]);
    }

    private function handleMenuSelection($phone,$message,$session)
    {
        if ($message == '1') {

            $products = Product::all();

            $response = "Products\n\n";

            foreach ($products as $product) {

                $response .=
                    "{$product->id}. ".
                    "{$product->name} - ".
                    "R{$product->price}\n";
            }

            $response .=
                "\nReply with product number";

            $session->update([
                'step' => 'select_product'
            ]);

            $this->sendMessage(
                $phone,
                $response
            );
        }

        return response()->json([
            'success' => true
        ]);
    }

    private function handleProductSelection($phone,$message,$session)
    {
        $product = Product::find($message);

        if (!$product) {

            $this->sendMessage(
                $phone,
                'Invalid product'
            );

            return response()->json([
                'success' => true
            ]);
        }

        $session->update([
            'product_id' => $product->id,
            'step' => 'quantity'
        ]);

        $this->sendMessage(
            $phone,
            "How many {$product->name} would you like?"
        );

        return response()->json([
            'success' => true
        ]);
    }

    private function handleQuantitySelection($phone,$message,$session)
    {
        $session->update([
            'quantity' => (int) $message,
            'step' => 'confirm'
        ]);

        $product = Product::find(
            $session->product_id
        );

        $total =
            $product->price *
            $message;

        $this->sendMessage(
            $phone,
            "Order Summary\n\n".
            "{$product->name}\n".
            "Qty: {$message}\n".
            "Total: R{$total}\n\n".
            "Reply YES to confirm"
        );

        return response()->json([
            'success' => true
        ]);
    }

    private function handleConfirmation($phone,$message,$session)
    {
        if (strtoupper($message) !== 'YES') {

            $this->sendMessage(
                $phone,
                'Order cancelled'
            );

            return response()->json([
                'success' => true
            ]);
        }

        $customer =
            Customer::firstOrCreate([
                'phone_number' => $phone
            ]);

        $product =
            Product::find(
                $session->product_id
            );

        $total =
            $product->price *
            $session->quantity;

        $order = Order::create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'total' => $total,
            'source' => 'whatsapp'
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $session->quantity,
            'price' => $product->price
        ]);

        ProcessOrderJob::dispatch(
            $order
        )->onQueue('orders');

        $this->sendMessage(
            $phone,
            "Order #{$order->id} created successfully"
        );

        $session->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
