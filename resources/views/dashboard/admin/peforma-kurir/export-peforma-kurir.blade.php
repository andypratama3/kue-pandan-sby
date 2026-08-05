<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Export Peforma Kurir</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            /* dari 11px */
            color: #222;
            line-height: 1.3;
        }

        h2 {
            margin: 0 0 4px 0;
            font-size: 14px;
            /* dari 18px */
            text-align: center;
        }

        h3 {
            margin: 18px 0 6px 0;
            font-size: 11px;
            /* dari 14px */
            border-left: 3px solid #444;
            padding-left: 6px;
        }

        .subtitle {
            text-align: center;
            font-size: 9px;
            /* dari 12px */
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th {
            background-color: #f2f2f2;
            border: 1px solid #555;
            padding: 5px;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
            /* dari 11px */
        }

        td {
            border: 1px solid #555;
            padding: 4px 5px;
            vertical-align: top;
            font-size: 8.8px;
        }

        .summary-table td {
            text-align: center;
            font-weight: normal;
        }

        .order-table th {
            font-size: 8.5px;
        }

        .order-table td {
            font-size: 8.5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .items-table td {
            border: none;
            padding: 2px 3px;
        }

        .items-table tr:not(:last-child) td {
            border-bottom: 0.5px dashed #ccc;
        }

        .items-product {
            font-weight: bold;
        }

        .items-variant {
            color: #666;
            font-size: 7.5px;
        }

        .items-qty {
            text-align: right;
            white-space: nowrap;
            font-size: 7.5px;
        }

        tr {
            page-break-inside: avoid;
        }

        .page-break {
            page-break-before: always;
        }
    </style>

</head>

<body>
    <h2>Ranking Peforma Kurir</h2>
    <div class="subtitle">Tanggal <b>{{ $daterange }}</b></div>

    @forelse($ranking as $row)
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Nama Kurir</th>
                    <th>Total Customer</th>
                    <th>Jumlah Pesanan Selesai</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $row['rank'] }}</td>
                    <td>{{ $row['nama_kurir'] }}</td>
                    <td>{{ $row['total_customer'] }}</td>
                    <td>{{ $row['jumlah_order'] }}</td>
                    <td>
                        Rp.{{ number_format($row['total'], 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>Rincian Pesanan {{ $row['nama_kurir'] }}</h3>
        <table class="order-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Invoice Number</th>
                    <th>Nama Customer</th>
                    <th>Detail Pesanan</th>
                    <th>total</th>
                </tr>
            </thead>
            <tbody>

                @forelse ($row["orders"] as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td>{{ $order->invoice_number }}</td>
                        <td>{{ $order->customer->name }}, {{ $order->customer->company_name }}</td>
                        <td style="padding:0;">
                            <table class="items-table">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="items-product">{{ $item->product->name }}</td>
                                        <td class="items-variant">{{ $item->variant_name ?? '-' }}</td>
                                        <td class="items-qty">x{{ $item->quantity }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                        <td>Rp.{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <p>Tidak ada transaksi</p>
                @endforelse
            </tbody>
        </table>
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
