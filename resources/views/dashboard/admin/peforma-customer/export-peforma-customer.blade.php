<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Export Peforma Customer</title>
	<style>
		body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
		table { border-collapse: collapse; width: 100%; margin-top: 20px; }
		th, td { border: 1px solid #333; padding: 6px 8px; text-align: center; }
		th { background: #eee; }
		h2 { margin-bottom: 0; }
		.subtitle { margin-top: 0; font-size: 14px; }
	</style>
</head>
<body>
	<h2>Ranking Peforma Customer</h2>
	<div class="subtitle">Bulan: <b>{{ $bulan }}</b></div>
	<table>
		<thead>
			<tr>
				<th>Rank</th>
				<th>Nama Customer</th>
				<th>Total Pembelian</th>
				<th>Total Retur</th>
				<th>Skor Akhir</th>
			</tr>
		</thead>
		<tbody>
			@forelse($ranking as $row)
			<tr>
				<td>{{ $row->peringkat }}</td>
				<td>{{ $row->nama_customer }}</td>
				<td>Rp. {{ number_format($row->total_pembelian, 0, ',', '.') }}</td>
				<td>Rp. {{ number_format($row->total_retur, 0, ',', '.') }}</td>
				<td>{{ $row->skor_akhir }}</td>
			</tr>
			@empty
			<tr>
				<td colspan="5">Belum ada data peforma customer bulan ini.</td>
			</tr>
			@endforelse
		</tbody>
	</table>
</body>
</html>
