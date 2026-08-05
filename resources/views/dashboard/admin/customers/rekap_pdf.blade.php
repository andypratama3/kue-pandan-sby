<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Order Customer</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>

    <h2>Rekap Order Customer</h2>
    <p><strong>Nama Customer:</strong> {{ $customer->name }}</p>
    <p><strong>Perusahaan:</strong> {{ $customer->company_name }}</p>
    <p><strong>Alamat:</strong> {{ $customer->address }}</p>
    <p><strong>Rentang Tanggal:</strong> {{ $start }} s/d {{ $end }}</p>

    @forelse($rekapPerOrder as $orderData)
        <div style="margin-top:30px;">
            <strong>Order #{{ $orderData['order']->invoice_number }}</strong><br>
            <span>Tanggal: {{ $orderData['order']->created_at->format('d-m-Y H:i') }}</span>
        </div>
        <table style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Varian</th>
                    <th>Jumlah Dipesan</th>
                    <th>Jumlah Retur</th>
                    <th>Terjual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orderData['produk'] as $i => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['product_name'] }}</td>
                        <td>{{ $row['variant_name'] ?? '-' }}</td>
                        <td>{{ $row['dipesan'] }}</td>
                        <td>{{ $row['retur'] }}</td>
                        <td>{{ $row['selisih'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">Tidak ada produk pada order ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @empty
        <div style="margin-top:20px;">Tidak ada order pada rentang tanggal ini.</div>
    @endforelse

    <h3 style="margin-top:20px;">Rekap Total Keseluruhan Order {{ $start }} s/d {{ $end }}</h3>
    <table style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Varian</th>
                <th>Jumlah Dipesan</th>
                <th>Jumlah Retur</th>
                <th>Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produkTotal as $i => $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row['product_name'] }}</td>
                    <td>{{ $row['variant_name'] ?? '-' }}</td>
                    <td>{{ $row['dipesan'] }}</td>
                    <td>{{ $row['retur'] }}</td>
                    <td>{{ $row['selisih'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Tidak ada produk pada rentang tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- KODE SCRIPT UNTUK NOMOR HALAMAN --}}
    <script type="text/php">
        if (isset($pdf)) {
            $fontMetrics = $pdf->getFontMetrics();
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 9;
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";

            // Mengambil lebar halaman
            $pageWidth = $pdf->get_width();

            // Menghitung lebar teks agar bisa diposisikan di kanan
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);

            // **PERUBAHAN UTAMA DI SINI**
            // Posisi X (horizontal): 40px dari tepi kanan
            $x = $pageWidth - $textWidth - 40;

            // Posisi Y (vertikal): 25px dari tepi atas
            $y = 25;

            // Tulis teks di setiap halaman
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
