<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #3b82f6;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background-color: #f9fafb;
        }

        .order-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #3b82f6;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Terima Kasih Atas Pesanan Anda!</h1>
        </div>

        <div class="content">
            <p>Halo {{ $order->user->name }},</p>

            <p>Pesanan Anda telah berhasil diproses. Berikut adalah detail pesanan Anda:</p>

            <div class="order-info">
                <strong>Order Number:</strong> {{ $order->order_number }}<br>
                <strong>Tanggal:</strong> {{ $order->created_at->format('d F Y H:i') }}<br>
                <strong>Total Belanja:</strong> Rp {{ number_format($order->total, 0, ',', '.') }}<br>
                <strong>Metode Pembayaran:</strong> {{ strtoupper($order->payment_method) }}<br>
                <strong>Status Pembayaran:</strong> {{ strtoupper($order->payment->status) }}
            </div>

            <p>Invoice pembelian Anda terlampir pada email ini dalam format PDF.</p>

            <p>Jika Anda memiliki pertanyaan tentang pesanan ini, silakan hubungi customer service kami.</p>

            <p>Terima kasih telah berbelanja di Toko Alat Kesehatan!</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Toko Alat Kesehatan. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
