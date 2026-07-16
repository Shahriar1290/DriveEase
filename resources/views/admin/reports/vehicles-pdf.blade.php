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
.meta { color: #6b7280; font-size: 11px; }
</style>
</head>
<body>
<div class="header">
    <h1>DriveEase — Vehicle Rental Report</h1>
    <p class="meta">Generated: {{ now()->format('M d, Y H:i') }}</p>
</div>
<table>
    <tr>
        <th>Vehicle</th>
        <th>Category</th>
        <th>Reg. No.</th>
        <th>Price/Day</th>
        <th>Total Rentals</th>
        <th>Completed</th>
        <th>Revenue</th>
        <th>Rating</th>
    </tr>
    @foreach($vehicles as $v)
    <tr>
        <td>{{ $v->vehicle_name }}</td>
        <td>{{ $v->category_name }}</td>
        <td>{{ $v->registration_number }}</td>
        <td>TK {{ number_format($v->price_per_day,2) }}</td>
        <td>{{ $v->total_rentals }}</td>
        <td>{{ $v->completed_bookings }}</td>
        <td>TK {{ number_format($v->total_revenue ?? 0,2) }}</td>
        <td>{{ $v->average_rating }} ⭐</td>
    </tr>
    @endforeach
</table>
</body>
</html>
