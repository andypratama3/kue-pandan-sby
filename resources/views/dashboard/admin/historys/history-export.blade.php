<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>History Pesanan - {{ $bulan }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            margin: 5px 0;
        }

        /* [!code focus:start] */
        .price-final {
            color: green;
            font-weight: bold;
        }

        .price-original {
            text-decoration: line-through;
            color: #999;
            font-size: 10px;
        }

        /* [!code focus:end] */
    </style>
</head>

<body>
    <div class="header">
        <h1>History Pesanan</h1>
        <p>Periode: {{ $bulan }}</p>
        <p>Region: {{ $regionName }}</p>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Kurir</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $order->invoice_number }}</td>
                    <td>{{ $order->created_at->isoFormat('D MMM YYYY') }}</td>
                    <td>{{ $order->customer->name ?? '-' }}</td>
                    <td>{{ $order->createdBy->name ?? '-' }}</td>
                    <td>
                        @if ($order->has_return)
                            <span class="price-final">
                                Rp {{ number_format($order->final_total, 0, ',', '.') }}
                            </span>
                            <br>
                            <span class="price-original">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        @else
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
