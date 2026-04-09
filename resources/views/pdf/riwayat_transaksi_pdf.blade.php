<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi - {{ $member->nama_lengkap }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0026e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0026e6;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #0026e6;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-membership { background-color: #e0e7ff; color: #4f46e5; }
        .badge-renewal { background-color: #dcfce7; color: #16a34a; }
        .badge-reactivation { background-color: #ffedd5; color: #ea580c; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>FURION GYM JAMBI</h1>
        <p>Laporan Riwayat Transaksi Member</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>ID Member</strong></td>
            <td width="35%">: {{ $member->id_members }}</td>
            <td width="15%"><strong>Dicetak Pada</strong></td>
            <td width="35%">: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Nama Lengkap</strong></td>
            <td>: {{ $member->nama_lengkap }}</td>
            <td><strong>Status</strong></td>
            <td>: {{ \Carbon\Carbon::parse($member->tanggal_selesai)->isFuture() ? 'Aktif' : 'Expired' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Tanggal</th>
                <th width="20%">Jenis Transaksi</th>
                <th width="35%">Nama Paket</th>
                <th width="20%" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayatTransaksi as $index => $transaksi)
                @php
                    $jenis = strtolower($transaksi->jenis_transaksi);
                    $badgeClass = 'badge-membership';
                    if($jenis == 'renewal') $badgeClass = 'badge-renewal';
                    if($jenis == 'reactivation') $badgeClass = 'badge-reactivation';
                    
                    // Sesuaikan nama kolom harga (nominal / total_pembayaran)
                    $harga = $transaksi->nominal ?? ($transaksi->total_pembayaran ?? 0);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $badgeClass }}">
                            {{ $jenis == 'membership' ? 'Pendaftaran' : ($jenis == 'renewal' ? 'Perpanjangan' : 'Reaktivasi') }}
                        </span>
                    </td>
                    <td>{{ $transaksi->nama_paket_snapshot ?? ($transaksi->paket->nama_paket ?? 'Paket Kustom') }}</td>
                    <td class="text-right">{{ number_format($harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Belum ada riwayat transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini di-generate secara otomatis oleh Sistem Furion Gym.</p>
    </div>

</body>
</html>