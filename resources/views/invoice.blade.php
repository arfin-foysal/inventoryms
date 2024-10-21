<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
        }
        .invoice-info div {
            width: 48%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Invoice</h1>
            <p>Ref: 86” IFP/BACBON EIBOARD/Invoice/{{ date('Ymd') }} | Date: {{ date('d-m-Y') }}</p>
        </div>

        <div class="invoice-info">
            <div>
                <h4>Billed To:</h4>
                <p>{{ $customer['name'] }}<br>
                {{ $customer['address'] }}<br>
                Concern Person: {{ $customer['contact'] }}</p>
            </div>
            <div>
                <h4>From:</h4>
                <p>BacBon Limited<br>
                Address: Your Address<br>
                Contact: Your Contact</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Item Name & Description</th>
                    <th>QTY</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['qty'] }}</td>
                    <td>{{ number_format($item['unit_price'], 2) }}</td>
                    <td>{{ number_format($item['total_price'], 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="total">Special Discount:</td>
                    <td>{{ number_format($discount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="total">Total Amount (Without VAT & TAX):</td>
                    <td>{{ number_format($total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="total">Advanced Paid:</td>
                    <td>{{ number_format($advanced_paid, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="total">Payable Due Amount:</td>
                    <td>{{ number_format($due_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <p>In Words: {{ $amount_in_words }}</p>

        <div class="signature">
            <div>
                <p>For {{ $customer['name'] }}</p>
                <p>_______________________</p>
                <p>Received Signature</p>
            </div>
            <div>
                <p>For BacBon Limited</p>
                <p>_______________________</p>
                <p>Authorized Signature</p>
            </div>
        </div>
    </div>
</body>
</html>
