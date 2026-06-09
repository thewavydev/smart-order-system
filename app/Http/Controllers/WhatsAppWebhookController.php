<?php

namespace App\Http\Controllers;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use App\Models\WhatsAppSession;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;

class WhatsAppWebhookController extends Controller
{
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
        $phone = str_replace('whatsapp:','',$request->From);

        $message = trim(strtolower($request->Body));

        $session = WhatsAppSession::firstOrCreate([
            'phone_number' => $phone
        ]);

        if ($message === 'hi' || $message === 'hello') {
            return $this->showMainMenu(
                $phone,
                $session
            );
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
