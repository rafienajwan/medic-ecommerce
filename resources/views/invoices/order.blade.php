<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .invoice-header {
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }

        .company-info {
            float: left;
            width: 50%;
        }

        .invoice-info {
            float: right;
            width: 45%;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        h1 {
            margin: 0;
            font-size: 28px;
            color: #000;
        }

        .customer-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table thead {
            background-color: #333;
            color: #fff;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .totals table {
            margin: 0;
        }

        .totals table td {
            border: none;
            padding: 5px 10px;
        }

        .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #f5f5f5;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .payment-method {
            margin: 20px 0;
            padding: 10px;
            background-color: #e3f2fd;
        }
    </style>
</head>

<body>
    <div class="invoice-header">
        <div class="company-info">
            <h1>MEDIC E-COMMERCE</h1>
            <p>
                Medical Equipment & Supplies<br>
                Jl. Kesehatan No. 123<br>
                Jakarta, Indonesia 12345<br>
                Phone: +62 21 1234 5678<br>
                Email: info@medicecommerce.com
            </p>
        </div>
        <div class="invoice-info">
            <h2 style="margin: 0;">INVOICE</h2>
            <p>
                <strong>Invoice Number:</strong> {{ $order->order_number }}<br>
                <strong>Date:</strong> {{ $order->created_at->format('d F Y') }}<br>
                <strong>Status:</strong> {{ ucfirst($order->status) }}<br>
                <strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}
            </p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="customer-info">
        <h3 style="margin-top: 0;">BILL TO:</h3>
        <p>
            <strong>{{ $order->shipping_name }}</strong><br>
            {{ $order->shipping_email }}<br>
            {{ $order->shipping_phone }}<br>
            {{ $order->shipping_address }}<br>
            {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}
        </p>
    </div>

    <div class="payment-method">
        <strong>Payment Method:</strong>
        @if ($order->payment_method === 'prepaid')
            Prepaid (Credit/Debit Card)
        @elseif($order->payment_method === 'postpaid')
            Postpaid
        @elseif($order->payment_method === 'cod')
            Cash on Delivery (COD)
        @elseif($order->payment_method === 'paypal')
            PayPal
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>SKU</th>
                <th class="text-right">Price</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->product_sku }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tax (11%):</td>
                <td class="text-right">Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Shipping Fee:</td>
                <td class="text-right">Rp {{ number_format($order->shipping_fee, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL:</td>
                <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    @if ($order->notes)
        <div style="margin-top: 30px;">
            <strong>Notes:</strong><br>
            {{ $order->notes }}
        </div>
    @endif

    <div class="footer">
        <p>
            Thank you for your business!<br>
            This is a computer-generated invoice and does not require a signature.<br>
            For any questions, please contact us at info@medicecommerce.com
        </p>
    </div>
</body>

</html>
