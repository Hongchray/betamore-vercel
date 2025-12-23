<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 13px;
            margin: 0;
            background: #f9fafc;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 5px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            padding: 10px;
        }

        /* HEADER */
        .header {
            width: 100%;
            border-bottom: 3px solid #6f42c1;
            margin-bottom: 25px;
        }
        .header td {
            vertical-align: middle;
            padding: 10px 0;
        }
        .logo {
            height: 60px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h1 {
            margin: 0;
            font-size: 26px;
            color: #6f42c1;
        }
        .invoice-title p {
            margin: 0;
            color: #666;
            font-size: 12px;
        }

        /* SECTION HEADINGS */
        h3 {
            font-size: 15px;
            color: #6f42c1;
            margin: 0 0 8px 0;
        }

        /* INFO BOXES */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .info-table .title {
            text-align: left;
            color: #1e40af;       /* Blue text */
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 6px;
            border-left: 4px solid #1e40af;  /* Blue left border */
            padding-left: 8px;     /* Space between border and text */
        }
        .info-table td {
            width: 33.3%;
            vertical-align: top;
            padding: 10px;
        }
        .box {
            /* border: 1px solid #e4e6eb; */
            /* border-radius: 6px; */
            padding: 12px;
            /* background: #fafbfc; */
        }
        p { margin: 4px 0; }

        /* ITEMS */
        .items {
            width: 100%;
            border-collapse: collapse;
        }
        .items th, .items td {
            border: 1px solid #e4e6eb;
            padding: 8px;
            font-size: 13px;
        }
        .items th {
            background: #f3f0fa;
            color: #4a3a75;
            text-align: left;
        }

        /* TOTALS */
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals p {
            margin: 4px 0;
            font-size: 13px;
        }
        .totals p:last-child {
            font-weight: bold;
            font-size: 15px;
            color: #6f42c1;
        }

        /* STATUS */
        .status {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            background: #f1f3f5;
            color: #444;
        }

        /* NOTE */
        .note {
            margin-top: 20px;
            background: #fff8e1;
            border-left: 5px solid #ffca28;
            padding: 10px 12px;
            font-style: italic;
            border-radius: 4px;
            color: #6d4c00;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            font-size: 11px;
            color: #888;
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <table class="header">
        <tr>
            <td><img src="{{ public_path('logo-primary.svg') }}" alt="Logo" class="logo"></td>
            <td class="invoice-title">
                <h1>Invoice</h1>
                <p>#{{ $order->order_number }}</p>
                <p>{{ $order->created_at->format('F d, Y') }}</p>
            </td>
        </tr>
    </table>

    <!-- INFO -->
    <table class="info-table">
        <tr>
            <td>
                <div class="title">Invoice Details</div>
                <div class="box">
                    <p><strong>Status:</strong> <span class="status">{{ ucfirst($order->status) }}</span></p>
                    <p><strong>Payment Method:</strong> {{ $order->orderPayment->paymentMethod->name ?? 'N/A' }}</p>
                </div>
            </td>

            <td>
                <div class="title">Customer</div>
                <div class="box">
                    <p><strong>Name:</strong> {{ $order->user->first_name }} {{ $order->user->last_name }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p><strong>Phone:</strong> {{ $order->user->phone }}</p>
                </div>
            </td>
            <td>
                <div class="title">Shipping</div>
                <div class="box">
                    <p><strong>Method:</strong> {{ $order->delivery->name ?? 'N/A' }}</p>
                    <p><strong>To:</strong> {{ $order->address->address ?? '' }}, {{ $order->address->city ?? '' }} {{ $order->address->postal_code ?? '' }}</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- ITEMS -->
    <h3>Items</h3>
    <table class="items">
        <thead>
        <tr>
            <th>Description</th>
            <th>Unit Price</th>
            <th>Qty</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->orderItems as $item)
            <tr>
                <td>
                    <strong>{{ $item->name }}</strong><br>
                    @if($item->modification)
                        <small>{{ $item->modification->modification_name }} ({{ $item->modification->unit }})</small>
                    @endif
                </td>
                <td>${{ number_format($item->unit_price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="totals">
        <p>Subtotal: ${{ number_format($order->total_price, 2) }}</p>
        <p>Delivery Fee: ${{ number_format($order->delivery_fee, 2) }}</p>
        <p>Total Amount: ${{ number_format($order->total_amount, 2) }}</p>
    </div>

    <!-- NOTE -->
    @if($order->note)
        <div class="note">
            <strong>Note:</strong> {{ $order->note }}
        </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        Thank you for your business!<br>
        Need help? Contact us at <a href="mailto:hello@focuzsolution.com">hello@focuzsolution.com</a>
    </div>

</div>
</body>
</html>
