<?php

namespace App\Http\Controllers\API;

use Midtrans\Config;
use App\Models\Booking;
use Midtrans\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class MidtransController extends Controller
{
    public function callback()
    {
        // Set konfigurasi midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        try {
            // Buat instance midtrans notification
            $notification = new Notification();

            // Assign ke variable untuk memudahkan coding
            $status = $notification->transaction_status;
            $type = $notification->payment_type;
            $fraud = $notification->fraud_status;
            $orderId = $notification->order_id;

            // Cari transaksi berdasarkan booking_code
            $booking = Booking::where('booking_code', $orderId)->first();

            if (!$booking) {
                return response()->json([
                    'meta' => [
                        'code' => 404,
                        'message' => 'Transaction not found',
                    ],
                ], 404);
            }

            // Handle notification status midtrans
            switch ($status) {
                case 'capture':
                    if ($type == 'credit_card') {
                        $booking->payment_status = $fraud == 'challenge' ? 'pending' : 'success';
                    }
                    break;
                case 'settlement':
                    $booking->payment_status = 'success';
                    break;
                case 'pending':
                    $booking->payment_status = 'pending';
                    break;
                case 'deny':
                case 'expire':
                case 'cancel':
                    $booking->payment_status = 'cancelled';
                    break;
            }

            // Kirim notifikasi email
            // Mail::to($booking->user->email)->send(new OrderNotification($booking, $booking->payment_status));

            // Simpan transaksi
            $booking->save();

            // Optional: Kirim notifikasi email
            // $this->sendPaymentNotification($transaction);

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'message' => 'Midtrans Notification Processed Successfully',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error: ' . $e->getMessage());

            return response()->json([
                'meta' => [
                    'code' => 500,
                    'message' => 'Internal Server Error',
                    'error' => $e->getMessage()
                ],
            ], 500);
        }
    }

    public function finishRedirect(Request $request)
    {
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $transactionStatus = $request->input('transaction_status');

        // Cari transaksi berdasarkan booking_code
        $booking = Booking::where('booking_code', $orderId)->first();

        if (!$booking) {
            return redirect()->route('transaction.not.found')->with('error', 'Transaksi tidak ditemukan');
        }

        // Tentukan view berdasarkan status transaksi
        switch (true) {
            case ($statusCode == 200 && $transactionStatus == 'settlement'):
                return view('pages.confirmations.success', [
                    'booking' => $booking,
                ]);

            case ($statusCode == 201 && $transactionStatus == 'pending'):
                return view('pages.confirmations.unfinish', [
                    'booking' => $booking,
                ]);

            default:
                return view('pages.confirmations.failed', [
                    'booking' => $booking,
                ]);
        }
    }

    public function unfinishRedirect(Request $request)
    {
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $transactionStatus = $request->input('transaction_status');

        // Cari transaksi berdasarkan booking_code
        $booking = Booking::where('booking_code', $orderId)->first();

        if (!$booking) {
            return redirect()->route('transaction.not.found')->with('error', 'Transaksi tidak ditemukan');
        }

        if ($statusCode == 201 && $transactionStatus == 'pending') {
            return view('pages.confirmations.unfinish', [
                'booking' => $booking,
            ]);
        }

        // Redirect ke halaman gagal jika tidak sesuai kondisi
        return view('pages.confirmations.failed', [
            'booking' => $booking,
        ]);
    }

    public function errorRedirect(Request $request)
    {
        $orderId = $request->input('order_id');

        // Cari transaksi berdasarkan booking_code
        $booking = Booking::where('booking_code', $orderId)->first();

        return view('pages.confirmations.failed', [
            'booking' => $booking,
        ]);
    }
}
