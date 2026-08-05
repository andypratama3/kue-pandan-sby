@php
// Deteksi mode PDF (Dompdf)
$isPdf = request()->routeIs('admin.historys.download');
@endphp
@if(!$isPdf)
<script>
    // Hapus header/footer print browser
    window.addEventListener('DOMContentLoaded', function() {
        // Hapus header/footer print dengan CSS
        const style = document.createElement('style');
        style.innerHTML = `
                @media print {
                    @page { margin: 0; }
                    body { margin: 0; }
                    /* Hapus header/footer print browser */
                    body::before, body::after { display: none !important; content: none !important; }
                }
            `;
        document.head.appendChild(style);
    });
</script>
@endif
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->invoice_number }}</title>
    @if(!$isPdf)
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
            }

            body>div.print-container {
                margin-top: 1cm !important;
                padding-top: 1cm !important;
            }
        }
    </style>
    @else
    <style>
        @page {
            size: A4;
            margin: 6mm;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
            background: #fff;
            font-size: 9px;
            line-height: 1.1;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            border: 1px solid #000;
            padding: 6px;
            background: #fff;
            box-sizing: border-box;
            height: auto;
            max-height: 45vh;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .header-left {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .header-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            text-align: right;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 3px 0;
            color: #000;
        }

        .invoice-number {
            font-size: 9px;
            color: #000;
            margin: 0.5px 0;
        }

        .invoice-date {
            font-size: 9px;
            color: #000;
            margin: 0.5px 0;
        }

        .brand-kue {
            font-size: 12px;
            color: #97b67d;
            margin: 0;
            font-weight: normal;
        }

        .brand-pandan {
            font-size: 14px;
            color: #97b67d;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .kurir-info {
            font-size: 9px;
            color: #000;
            margin: 3px 0 0 0;
        }

        .customer-section {
            margin-bottom: 8px;
        }

        .customer-label {
            font-size: 10px;
            font-weight: bold;
            margin: 0 0 1px 0;
            color: #000;
        }

        .customer-name {
            font-size: 10px;
            font-weight: bold;
            margin: 0 0 1px 0;
            color: #000;
        }

        .customer-details {
            font-size: 9px;
            margin: 0 0 0.5px 0;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px 4px;
            font-size: 9px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background: #f5f5f5;
        }

        .footer-signature {
            margin-top: 8px;
            font-size: 9px;
            color: #000;
            padding-bottom: 3px;
        }
    </style>
    @endif
</head>

<body @if(!$isPdf) class="bg-white p-8 print:p-0" @endif>
    <div @if($isPdf) class="container" @else class="print-container max-w-2xl mx-auto bg-white border border-gray-300 rounded-xl shadow p-8 mt-8" @endif>
        <!-- Header -->
        @if($isPdf)
        <div class="header">
            <div class="header-left">
                <h1 class="invoice-title">INVOICE</h1>
                <div class="invoice-number">{{ $order->invoice_number }}</div>
                <div class="invoice-date">Tanggal: {{ $order->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</div>
            </div>
            <div class="header-right">
                <div class="brand-kue">Kue Ijo</div>
                <div class="brand-pandan">PANDAN ASLI</div>
                <div class="kurir-info">Kurir: {{ $order->createdBy->name ?? '-' }}</div>
            </div>
        </div>
        @else
        <div class="flex flex-row justify-between items-start mb-8">
            <div class="flex flex-col items-start">
                <h1 class="text-3xl font-bold leading-tight mb-1">INVOICE</h1>
                <div class="text-base text-gray-500 font-mono mb-0.5">{{ $order->invoice_number }}</div>
                <div class="text-base text-gray-500 font-mono">Tanggal: {{ $order->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</div>
            </div>
            <div class="flex flex-col items-end text-right">
                <span class="text-lg font-normal leading-tight" style="color:#97b67d; font-family:serif;">Kue Ijo</span>
                <span class="text-3xl font-extrabold tracking-wider mt-0 -mb-2" style="color:#97b67d; font-family:serif; letter-spacing:2px;">PANDAN ASLI</span>
                <span class="text-base text-gray-700 mt-2">Kurir: {{ $order->createdBy->name ?? '-' }}</span>
            </div>
        </div>
        @endif

        <!-- Customer Info & Products Table (Browser) -->
        @if(!$isPdf)
        <div class="mb-6">
            <div class="font-bold text-base mb-1">Kepada Yth:</div>
            <div class="font-bold text-base mb-1">{{ $order->customer->name ?? 'Kiki' }}</div>
            <div class="text-sm mb-1">{{ $order->customer->phone ?? '628133651455' }}</div>
            <div class="text-sm">{{ $order->address ?? 'Jl Patimura' }}</div>
        </div>
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2">Produk</th>
                        <th class="border px-3 py-2">Varian</th>
                        <th class="border px-3 py-2 text-center">Qty</th>
                        <th class="border px-3 py-2 text-right">Harga</th>
                        <th class="border px-3 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td class="border px-3 py-2">{{ $item->product_name }}</td>
                        <td class="border px-3 py-2">{{ $item->variant_name ?? '-' }}</td>
                        <td class="border px-3 py-2 text-center">{{ $item->quantity }}</td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="4" class="border px-3 py-2 text-right">Total</td>
                        <td class="border px-3 py-2 text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
            @php
            $activeReturn = $order->returns->first();
            @endphp
            @if($activeReturn && $activeReturn->returnedProducts->count())
            <div class="mt-4">
                <div class="font-bold text-sm text-red-700 mb-1">Daftar Produk Retur:</div>
                <table class="min-w-full border text-xs">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">Produk</th>
                            <th class="border px-2 py-1">Varian</th>
                            <th class="border px-2 py-1 text-center">Qty Retur</th>
                            <th class="border px-2 py-1 text-right">Harga</th>
                            <th class="border px-2 py-1 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeReturn->returnedProducts as $retur)
                        <tr>
                            <td class="border px-2 py-1">{{ $retur->product->name ?? '-' }}</td>
                            <td class="border px-2 py-1">{{ $retur->variant->name ?? '-' }}</td>
                            <td class="border px-2 py-1 text-center">{{ $retur->quantity }}</td>
                            <td class="border px-2 py-1 text-right">Rp {{ number_format($retur->price, 0, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right text-bold">Rp {{ number_format($retur->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 font-bold">
                            <td colspan="4" class="border px-3 py-2 text-right">Subtotal Final</td>
                            <td class="border px-3 py-2 text-right text-green-700">
                                Rp {{ number_format($order->total_amount - $activeReturn->total_amount_returned, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>

                </table>
            </div>
            @endif
        </div>
        @else
        <div class="customer-section">
            <div class="customer-label">Kepada Yth:</div>
            <div class="customer-name">{{ $order->customer->name ?? 'Kiki' }}</div>
            <div class="customer-details">{{ $order->customer->phone ?? '628133651455' }}</div>
            <div class="customer-details">{{ $order->address ?? 'Jl Patimura' }}</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Varian</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->variant_name ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>Total</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
        @php
        $activeReturn = $order->returns->first();
        @endphp
        @if($activeReturn && $activeReturn->returnedProducts->count())
        <div style="margin-top:8px;">
            <div style="font-weight:bold;font-size:10px;color:#b91c1c;margin-bottom:2px;">Daftar Produk Retur:</div>
            <table style="width:100%;border-collapse:collapse;font-size:9px;">
                <thead>
                    <tr>
                        <th style="border:1px solid #000;padding:2px 4px;text-align:left;">Produk</th>
                        <th style="border:1px solid #000;padding:2px 4px;text-align:left;">Varian</th>
                        <th style="border:1px solid #000;padding:2px 4px;text-align:center;">Qty Retur</th>
                        <th style="border:1px solid #000;padding:2px 4px;text-align:right;">Harga</th>
                        <th style="border:1px solid #000;padding:2px 4px;text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeReturn->returnedProducts as $retur)
                    <tr>
                        <td style="border:1px solid #000;padding:2px 4px;text-align:left;">{{ $retur->product->name ?? '-' }}</td>
                        <td style="border:1px solid #000;padding:2px 4px;text-align:left;">{{ $retur->variant->name ?? '-' }}</td>
                        <td style="border:1px solid #000;padding:2px 4px;text-align:center;">{{ $retur->quantity }}</td>
                        <td style="border:1px solid #000;padding:2px 4px;text-align:right;">Rp {{ number_format($retur->price, 0, ',', '.') }}</td>
                        <td style="border:1px solid #000;padding:2px 4px;text-align:right;">Rp {{ number_format($retur->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="width:100%;text-align:right;margin-top:4px;">
                <span style="font-weight:bold;">Subtotal:</span>
                <span style="font-weight:bold;color:#15803d;">
                    Rp {{ number_format($order->total_amount - $activeReturn->total_amount_returned, 0, ',', '.') }}
                </span>
                <span style="display:block;font-size:8px;color:#888;margin-top:2px;">(Total Awal Rp {{ number_format($order->total_amount, 0, ',', '.') }} - Retur Rp {{ number_format($activeReturn->total_amount_returned, 0, ',', '.') }})</span>
            </div>
        </div>
        @endif
        @endif

        <!-- Footer -->
        <div @if($isPdf) class="footer-signature text-left" @else class="mt-6 text-left" @endif>
            <p>Hormat Kami,<br>Dian</p>
        </div>

        @if(!$isPdf)
        <div class="mt-8 flex justify-between items-center">
            <div class="no-print flex gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Print Invoice</button>
                <button onclick="window.close()" class="px-4 py-2 bg-red-400 text-white rounded hover:bg-gray-600">Tutup</button>
            </div>
        </div>
        @endif
    </div>
</body>

</html>