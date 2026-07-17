<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $booking->booking_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        .invoice-container { max-width: 780px; margin: 2rem auto; background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.1); }
        .invoice-header { background: linear-gradient(135deg, #0f172a, #0ea5e9); color: white; padding: 2rem; }
        .invoice-body { padding: 2rem; }
        .invoice-meta { background: #f8fafc; border-radius: .5rem; padding: 1.25rem; }
        .divider { border-color: #e2e8f0; }
        .status-badge { display: inline-block; padding: .35rem .75rem; border-radius: 2rem; font-size: .78rem; font-weight: 600; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-completed { background: #dbeafe; color: #1e40af; }
        @media print {
            body { background: white; }
            .invoice-container { box-shadow: none; margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <div class="invoice-header d-flex justify-content-between align-items-start">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-car-side me-2"></i>DriveEase</h2>
            <p class="mb-0 opacity-75 small">123 Main Street, Dhaka, Bangladesh</p>
            <p class="mb-0 opacity-75 small">info@driveease.com · +880 1700-000000</p>
        </div>
        <div class="text-end">
            <div class="h4 fw-bold mb-1">INVOICE</div>
            <div class="opacity-75 small">#{{ $booking->booking_number }}</div>
            <div class="opacity-75 small">{{ now()->format('M d, Y') }}</div>
            <span class="status-badge {{ $booking->booking_status === 'completed' ? 'status-completed' : 'status-approved' }} mt-2 d-inline-block">
                {{ strtoupper($booking->booking_status) }}
            </span>
        </div>
    </div>

    <div class="invoice-body">
        {{-- Billed To / Vehicle --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Billed To</h6>
                <p class="fw-bold mb-1">{{ $user->name }}</p>
                <p class="text-muted small mb-1">{{ $user->email }}</p>
                <p class="text-muted small mb-0">{{ $user->phone ?? 'N/A' }}</p>
                @if($user->address)
                <p class="text-muted small mb-0">{{ $user->address }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Vehicle Details</h6>
                <p class="fw-bold mb-1">{{ $vehicle->vehicle_name }}</p>
                <p class="text-muted small mb-1">Reg: {{ $vehicle->registration_number }}</p>
                <p class="text-muted small mb-1">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }}</p>
                <p class="text-muted small mb-0">{{ ucfirst($vehicle->fuel_type) }} · {{ ucfirst($vehicle->transmission) }}</p>
            </div>
        </div>

        {{-- Rental Details --}}
        <div class="invoice-meta mb-4">
            <div class="row g-3">
                <div class="col-6 col-md-3 text-center">
                    <div class="text-muted small">Pickup Date</div>
                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($booking->pickup_date)->format('M d, Y') }}</div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <div class="text-muted small">Return Date</div>
                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($booking->return_date)->format('M d, Y') }}</div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <div class="text-muted small">Duration</div>
                    <div class="fw-semibold">{{ $booking->total_days }} Days</div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <div class="text-muted small">Daily Rate</div>
                    <div class="fw-semibold">TK {{ number_format($booking->price_per_day,2) }}</div>
                </div>
            </div>
        </div>

        {{-- Line Items --}}
        <table class="table mb-4">
            <thead style="background:#f8fafc">
                <tr>
                    <th class="border-0 py-3">Description</th>
                    <th class="border-0 py-3 text-center">Qty</th>
                    <th class="border-0 py-3 text-end">Rate</th>
                    <th class="border-0 py-3 text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border-0 py-3">
                        <div class="fw-semibold">Vehicle Rental — {{ $vehicle->vehicle_name }}</div>
                        <div class="text-muted small">{{ \Carbon\Carbon::parse($booking->pickup_date)->format('M d') }} to {{ \Carbon\Carbon::parse($booking->return_date)->format('M d, Y') }}</div>
                    </td>
                    <td class="border-0 py-3 text-center">{{ $booking->total_days }} days</td>
                    <td class="border-0 py-3 text-end">TK {{ number_format($booking->price_per_day,2) }}</td>
                    <td class="border-0 py-3 text-end">TK {{ number_format($booking->total_cost,2) }}</td>
                </tr>
                @if($booking->discount > 0)
                <tr>
                    <td colspan="3" class="border-0 text-end text-muted">Discount</td>
                    <td class="border-0 text-end text-success">-TK {{ number_format($booking->discount,2) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" class="border-0 text-end text-muted">Tax (5%)</td>
                    <td class="border-0 text-end">TK {{ number_format($booking->tax,2) }}</td>
                </tr>
            </tbody>
            <tfoot style="background:#f8fafc">
                <tr>
                    <td colspan="3" class="fw-bold py-3 text-end border-0">Total Amount</td>
                    <td class="fw-bold py-3 text-end text-primary border-0 fs-5">TK {{ number_format($booking->final_amount,2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Payment Info --}}
        @if($payment)
        <div class="invoice-meta mb-4">
            <h6 class="fw-bold small text-uppercase mb-3">Payment Information</h6>
            <div class="row g-2 small">
                <div class="col-md-4"><span class="text-muted">Transaction ID:</span><br><strong>{{ $payment->transaction_id }}</strong></div>
                <div class="col-md-4"><span class="text-muted">Receipt Number:</span><br><strong>{{ $payment->receipt_number }}</strong></div>
                <div class="col-md-4"><span class="text-muted">Payment Method:</span><br><strong>{{ ucwords(str_replace('_',' ',$payment->payment_method)) }}</strong></div>
            </div>
        </div>
        @endif

        <div class="text-center text-muted small border-top pt-4">
            <p class="mb-1">Thank you for choosing DriveEase!</p>
            <p class="mb-0">For support: info@driveease.com · +880 1700-000000</p>
        </div>
    </div>
</div>

<div class="text-center my-3 no-print">
    <button onclick="window.print()" class="btn btn-primary me-2">
        <i class="fas fa-print me-1"></i>Print Invoice
    </button>
    <a href="{{ route('customer.bookings.show', $booking->id) }}" class="btn btn-light border">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>
</body>
</html>
