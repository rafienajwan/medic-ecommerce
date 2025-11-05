<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 18px;
            margin-top: 20px;
        }

        .info-section {
            margin: 20px 0;
        }

        .info-row {
            margin: 5px 0;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background-color: #3b82f6;
            color: white;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            padding: 5px 0;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            clear: both;
        }

        .payment-info {
            background-color: #f0f9ff;
            padding: 10px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">Toko Alat Kesehatan</div>
        <div>Jl. Kesehatan No. 123, Jakarta 12345</div>
        <div>Telp: (021) 1234-5678 | Email: info@tokoalatkes.com</div>
        <div class="invoice-title">INVOICE</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">Order Number:</span>
            <span>{{ $order->order_number }}</span>
        </div>
        <div class="info-row">
            <span class="label">Date:</span>
            <span>{{ $order->created_at->format('d F Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="label">User ID:</span>
            <span>{{ $order->user->email }}</span>
        </div>
        <div class="info-row">
            <span class="label">Name:</span>
            <span>{{ $order->user->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">Phone:</span>
            <span>{{ $order->shipping_phone }}</span>
        </div>
        <div class="info-row">
            <span class="label">Address:</span>
            <span>{{ $order->shipping_address }}, {{ $order->shipping_city }}</span>
        </div>
    </div>

    <div class="payment-info">
        <div class="info-row">
            <span class="label">Payment Method:</span>
            <span>{{ strtoupper($order->payment_method) }}
                @if ($order->payment_method === 'prepaid')
                    ({{ $order->payment->payment_details['card_type'] ?? 'Card' }})
                @elseif($order->payment_method === 'paypal')
                    (PayPal)
                @elseif($order->payment_method === 'cod')
                    (Cash on Delivery)
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="label">Payment Status:</span>
            <span style="color: {{ $order->payment->isCompleted() ? '#22c55e' : '#f59e0b' }};">
                {{ strtoupper($order->payment->status) }}
            </span>
        </div>
        @if ($order->payment->transaction_ref)
            <div class="info-row">
                <span class="label">Transaction Ref:</span>
                <span>{{ $order->payment->transaction_ref }}</span>
            </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Product</th>
                <th>SKU</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->product_sku }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Tax (10%):</span>
            <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Shipping Fee:</span>
            <span>Rp {{ number_format($order->shipping_fee, 0, ',', '.') }}</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <p><strong>TANDATANGAN TOKO</strong></p>
        <p>Terima kasih atas pembelian Anda!</p>
        <p>Invoice ini dibuat secara otomatis oleh sistem.</p>
        <p>Untuk pertanyaan, hubungi customer service kami.</p>
    </div>
</body>

</html>
