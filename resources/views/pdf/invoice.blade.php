<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Invoice Furion Gym' }}</title>
    <style>
        /* Reset CSS dasar untuk PDF */
        * { box-sizing: border-box; }
        body {
            font-family: sans-serif; /* Jangan gunakan font aneh-aneh dulu */
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 0; padding: 0;
        }
        .container { width: 100%; padding: 20px; }
        
        /* Header */
        .header { width: 100%; border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 10px; }
        .header td { vertical-align: top; }
        .logo { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #000; }
        .company-info { text-align: right; font-size: 11px; color: #555; }

        /* Invoice Meta */
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { vertical-align: top; }
        .bill-to { width: 60%; }
        .invoice-data { width: 40%; text-align: right; }
        
        .status-paid {
            background-color: #2ecc71; color: white; padding: 5px 10px;
            font-weight: bold; border-radius: 4px; display: inline-block;
        }

        /* Tabel Produk */
        .items-table {
            width: 100%; border-collapse: collapse; margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f2f2f2; border-bottom: 1px solid #ddd;
            padding: 8px; text-align: left; font-weight: bold;
        }
        .items-table td {
            border-bottom: 1px solid #eee; padding: 8px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .total-row td { font-weight: bold; background-color: #f9f9f9; border-top: 2px solid #333; }

        /* Footer */
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #888; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <table class="header">
            <tr>
                <td>
                    <div class="logo">FURION GYM</div>
                    <div>Member Management System</div>
                </td>
                <td class="company-info">
                    <strong>Furion Gym Jambi</strong><br>
                    Jl. Contoh Lokasi No. 123, Jambi<br>
                    Telp: (0741) 1234567 | Admin: {{ Auth::user()->name ?? 'Admin' }}
                </td>
            </tr>
        </table>

        <table class="meta-table">
            <tr>
                <td class="bill-to">
                    <strong>DITAGIHKAN KEPADA:</strong><br>
                    Nama: {{ $member->nama_lengkap }}<br>
                    Telp: {{ $member->no_telepon }}<br>
                    Email: {{ $member->email }}<br>
                    ID: {{ $member->id_members }}
                </td>
                <td class="invoice-data">
                    <h2 style="margin: 0 0 10px 0;">INVOICE</h2>
                    <strong>No:</strong> {{ $payment->nomor_invoice }}<br>
                    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($payment->tanggal_transaksi)->format('d/m/Y') }}<br>
                    <br>
                    <span class="status-paid">LUNAS</span>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th class="text-center">Durasi</th>
                    <th class="text-center">Periode</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $paket->nama_paket }}</strong><br>
                        <small>Registrasi Member Baru</small>
                    </td>
                    <td class="text-center">{{ intval($paket->durasi) }} Bulan</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($member->tanggal_daftar)->format('d/m/y') }} - 
                        {{ \Carbon\Carbon::parse($member->tanggal_selesai)->format('d/m/y') }}
                    </td>
                    <td class="text-right">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL PEMBAYARAN</td>
                    <td class="text-right">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            Terima kasih telah bergabung dengan Furion Gym Jambi.<br>
            Invoice ini sah dan diterbitkan secara komputerisasi.
        </div>
    </div>
</body>
</html>