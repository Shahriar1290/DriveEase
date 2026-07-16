<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
h1 { font-size: 20px; color: #0ea5e9; margin-bottom: 4px; }
.header { border-bottom: 2px solid #0ea5e9; padding-bottom: 10px; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th { background: #0ea5e9; color: white; padding: 8px; text-align: left; font-size: 11px; }
td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
tr:nth-child(even) td { background: #f9fafb; }
.total { font-weight: bold; font-size: 14px; color: #16a34a; }
.meta { color: #6b7280; font-size: 11px; }
</style>
</head>
<body>
<div class="header">
    <h1>DriveEase — Revenue Report</h1>
    <p class="meta">Period: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
    <p class="meta">Generated: {{ now()->format('M d, Y H:i') }}</p>
</div>

<p><strong>Total Revenue: </strong><span class="total">TK {{ number_format($total,2) }}</span></p>
<p><strong>Total Transactions: </strong>{{ count($payments) }}</p>

<h3>By Payment Method</h3>
<table>
    <tr><th>Method</th><th>Transactions</th><th>Total</th></tr>
    @foreach($byMethod as $method => $data)
    <tr>
        <td>{{ ucwords(str_replace('_',' ',$method)) }}</td>
        <td>{{ $data['count'] }}</td>
        <td>TK {{ number_format($data['total'],2) }}</td>
    </tr>
    @endforeach
</table>

<h3>Transaction List</h3>
<table>
    <tr><th>Date</th><th>Transaction ID</th><th>Customer</th><th>Booking#</th><th>Method</th><th>Amount</th></tr>
    @foreach($payments as $p)
    <tr>
        <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
        <td>{{ $p->transaction_id }}</td>
        <td>{{ $p->user_name }}</td>
        <td>{{ $p->booking_number }}</td>
        <td>{{ ucwords(str_replace('_',' ',$p->payment_method)) }}</td>
        <td>TK {{ number_format($p->amount,2) }}</td>
    </tr>
    @endforeach
</table>
</body>
</html>
