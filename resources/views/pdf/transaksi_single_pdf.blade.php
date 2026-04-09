<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Receipt - Furion Gym</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .invoice-box {
            max-width: 100%;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background-color: #fff;
        }
        .header {
            border-bottom: 2px solid #0026e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .header td {
            padding: 5px;
            vertical-align: top;
        }
        .title {
            font-size: 28px;
            color: #0026e6;
            font-weight: bold;
            margin: 0;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
            margin: 5px 0 0 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th, .details-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .details-table th {
            background-color: #f8fafc;
            color: #0026e6;
            font-size: 12px;
            text-transform: uppercase;
        }
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            color: #0026e6;
            border-bottom: 2px solid #0026e6;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px dashed #ccc;
            padding-top: 15px;
        }
        .badge {
            background-color: #dcfce7;
            color: #16a34a;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>
    @php
        $jenis = strtolower($transaksi->jenis_transaksi);
        $jenisLabel = $jenis == 'membership' ? 'Pendaftaran' : ($jenis == 'renewal' ? 'Perpanjangan' : 'Reaktivasi');
        $harga = $transaksi->nominal ?? ($transaksi->total_pembayaran ?? 0);
    @endphp

    <div class="invoice-box">
        <div class="header">
            <table>
                <tr>
                    <td width="60%">
                        <h1 class="title">FURION GYM</h1>
                        <p class="subtitle">E-Receipt Transaksi Official</p>
                    </td>
                    <td width="40%" class="text-right">
                        <strong>Invoice ID:</strong> #{{ str_pad($transaksi->id, 6, '0', STR_PAD_LEFT) }}<br>
                        <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y') }}<br>
                        <strong>Status:</strong> <span class="badge">LUNAS</span>
                    </td>
                </tr>
            </table>
        </div>

        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td width="50%">
                    <h3 style="margin: 0 0 5px 0; font-size: 14px; color: #666;">Ditagihkan Kepada:</h3>
                    <strong>{{ $transaksi->member->nama_lengkap ?? 'Member Furion' }}</strong><br>
                    ID Member: {{ $transaksi->member->id_members ?? '-' }}
                </td>
                <td width="50%" class="text-right">
                    <h3 style="margin: 0 0 5px 0; font-size: 14px; color: #666;">Jenis Transaksi:</h3>
                    <strong>{{ $jenisLabel }} Membership</strong>
                </td>
            </tr>
        </table>

        <table class="details-table">
            <thead>
                <tr>
                    <th width="70%">Deskripsi Layanan</th>
                    <th width="30%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $transaksi->nama_paket_snapshot ?? ($transaksi->paket->nama_paket ?? 'Paket Membership') }}</strong><br>
                        <span style="font-size: 11px; color: #666;">Akses layanan gym Furion Jambi.</span>
                    </td>
                    <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="text-right">TOTAL PEMBAYARAN</td>
                    <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Terima kasih telah mempercayakan Furion Gym sebagai partner fitness Anda.<br>
            <i>Dokumen ini adalah bukti pembayaran yang sah dan di-generate otomatis oleh sistem.</i></p>
        </div>
    </div>
</body>
</html>