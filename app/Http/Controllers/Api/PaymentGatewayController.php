<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentGatewayController extends Controller
{
    public function payment($user_id)
    {

        try {
            //code...
            // $item_details = [];
            $payment = Payments::with('users')->where('user_id', $user_id)->where('status', 'Unpaid')->latest()->first();

            $item_details = [];

            $item_details[] = [
                'id' => $payment->id,
                'price' => $payment->price,
                'quantity' => 1,
                'name' => "Wedding Planner",
            ];

            // Payment gateway Midtrans

            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = false;
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = true;

            \Midtrans\Config::$overrideNotifUrl = config('app.url') . '/api/payment-callback';
            // \Midtrans\Config::$overrideNotifUrl = 'https://backend.alodia.site/api/callback';

            $order_id = $payment->id;
            $random_string = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
            $order_id_with_random = $order_id . $random_string;

            $params = array(
                'transaction_details' => array(
                    'order_id' => $order_id_with_random,
                    'gross_amount' => $payment->price,
                ),
                'customer_details' => array(
                    'first_name' => $payment->users->name,
                    'email' => $payment->users->email,
                ),
                'enabled_payments' => array(
                    "permata_va",
                    "bca_va",
                    "bni_va",
                    "bri_va",
                    "cimb_va",
                    "other_va",
                    "echannel",
                ),
                'item_details' => $item_details,


            );
            // return response()->json($params['transaction_details']['gross_amount']);
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            return response()->json(['data' => [
                'snapToken' => $snapToken,
            ], 'status' => 'Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {

        $serverKey = config('midtrans.serverKey');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        if ($hashed == $request->signature_key) {
            if (($request->transaction_status == 'capture' && $request->payment_type == 'credit_card' && $request->fraud_status == 'accept') or $request->transaction_status == 'settlement') {
                $numeric_part = preg_replace('/\D/', '', $request->order_id);
                $order = Payments::find($numeric_part);
                $metode_pembayaran = '';
                if ($request->payment_type === 'bank_transfer') {
                    $metode_pembayaran = $request->va_numbers[0]['bank'];
                } elseif ($request->payment_type === 'echannel') {
                    $metode_pembayaran = 'Mandiri Bill Payment';
                } elseif ($request->payment_type === 'cstore') {
                    $metode_pembayaran = isset($request->store) ? $request->store : 'cstore';
                } elseif ($request->payment_type === 'gopay') {
                    $metode_pembayaran = 'GoPay';
                } elseif ($request->payment_type === 'shopeepay') {
                    $metode_pembayaran = 'ShopeePay';
                } elseif ($request->payment_type === 'qris') {
                    $metode_pembayaran = 'QRIS';
                } elseif ($request->payment_type === 'bca_klikpay') {
                    $metode_pembayaran = 'BCA KlikPay';
                } elseif ($request->payment_type === 'bca_klikbca') {
                    $metode_pembayaran = 'BCA KlikBCA';
                } elseif ($request->payment_type === 'bri_epay') {
                    $metode_pembayaran = 'BRI Epay';
                } elseif ($request->payment_type === 'cimb_clicks') {
                    $metode_pembayaran = 'CIMB Clicks';
                } elseif ($request->payment_type === 'danamon_online') {
                    $metode_pembayaran = 'Danamon Online';
                } elseif ($request->payment_type === 'akulaku') {
                    $metode_pembayaran = 'Akulaku';
                } elseif ($request->payment_type === 'permata_va') {
                    $metode_pembayaran = 'Permata VA';
                } elseif ($request->payment_type === 'bni_va') {
                    $metode_pembayaran = 'BNI VA';
                } elseif ($request->payment_type === 'other_va') {
                    $metode_pembayaran = 'Other VA';
                } elseif ($request->payment_type === 'alfamart') {
                    $metode_pembayaran = 'Alfamart';
                } elseif ($request->payment_type === 'indomaret') {
                    $metode_pembayaran = 'Indomaret';
                } else {
                    $metode_pembayaran = $request->payment_type;
                }
                $order->update([
                    'status' => 'Paid',
                    'bank_type' => $metode_pembayaran

                ]);
            }
        }
    }
}
