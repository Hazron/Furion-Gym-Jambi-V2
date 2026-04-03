<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaketMember;
use App\Models\Members;
use App\Models\MembershipPayment;
use App\Models\paket_promo;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MemberAdminController extends Controller
{
    public function memberAdmin()
    {
        $paketReguler = PaketMember::where('status', 'aktif')->where('jenis', 'reguler')->get();
        $paketCouple = PaketMember::where('status', 'aktif')->where('jenis', 'couple')->get();
        $paketPromo = PaketMember::where('status', 'aktif')->where('jenis', 'promo')->get();
        $paketPromoCouple = PaketMember::where('status', 'aktif')->where('jenis', 'promo couple')->get();

        $members = Members::orderBy('updated_at', 'desc')->get();

        return view('Admin.Member', compact(
            'paketReguler',
            'paketCouple',
            'paketPromo',
            'paketPromoCouple',
            'members'
        ));
    }

    public function registerMember(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'nama_member' => 'required|string|max:255',
                'nomor_telepon' => 'required|string|max:20',
                'email' => 'required|email|unique:members,email',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'tanggal_mulai' => 'required|date',
                'id_paket' => 'required',
                'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
            ]);

            $skipInvoice = $request->has('skip_invoice');
            if ($skipInvoice) {
                $ownerPassword = env('OWNER_PASSWORD', 'admin123');
                if ($request->owner_password !== $ownerPassword) {
                    return redirect()->back()->with('error', 'Password Owner Salah! Tidak diizinkan melewati pembuatan invoice.')->withInput();
                }
            }

            $paket = PaketMember::where('id_paket', $request->id_paket)->firstOrFail();
            $hargaPaket = $paket->harga;
            $namaPaket = $paket->nama_promo ?? $paket->nama_paket;
            $durasiString = $paket->durasi;
            $savePaketId = $paket->id_paket;

            $isCouple = (in_array($paket->jenis, ['couple', 'promo couple']) || stripos($namaPaket, 'couple') !== false);

            $tanggalMulai = Carbon::parse($request->tanggal_mulai);
            $tanggalSelesai = $tanggalMulai->copy();

            preg_match('/\d+/', $durasiString, $matches);
            $jumlahDurasi = isset($matches[0]) ? (int) $matches[0] : 1;
            $durasiLower = strtolower($durasiString);

            if (str_contains($durasiLower, 'tahun')) {
                $tanggalSelesai->addYears($jumlahDurasi);
            } elseif (str_contains($durasiLower, 'hari')) {
                $tanggalSelesai->addDays($jumlahDurasi);
            } else {
                $tanggalSelesai->addMonths($jumlahDurasi);
            }

            $generateId = function () {
                do {
                    $id = 'FRN-' . mt_rand(100000, 999999);
                } while (Members::where('id_members', $id)->exists());
                return $id;
            };

            // 1. SIMPAN MEMBER UTAMA
            $idMember1 = $generateId();
            $member1 = Members::create([
                'id_members' => $idMember1,
                'nama_lengkap' => $request->nama_member,
                'alamat' => '-',
                'no_telepon' => $request->nomor_telepon,
                'email' => $request->email,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_daftar' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'paket_id' => $savePaketId,
                'status' => 'active',
                'partner_id' => null,
            ]);

            $namaPartnerStr = '';
            if ($isCouple) {
                if ($request->filled('partner_id')) {
                    $partner = Members::findOrFail($request->partner_id);

                    $partner->update([
                        'paket_id' => $savePaketId,
                        'tanggal_selesai' => $tanggalSelesai,
                        'status' => 'active',
                        'partner_id' => $member1->id_members,
                    ]);

                    $member1->partner_id = $partner->id_members;
                    $member1->save();

                    $namaPartnerStr = ' w/ ' . $partner->nama_lengkap;
                } elseif ($request->filled('nama_member_2')) {
                    $idMember2 = $generateId();
                    while ($idMember2 === $idMember1) {
                        $idMember2 = $generateId();
                    }

                    $member2 = Members::create([
                        'id_members' => $idMember2,
                        'nama_lengkap' => $request->nama_member_2,
                        'alamat' => '-',
                        'no_telepon' => $request->nomor_telepon_2,
                        'email' => $request->email_2,
                        'jenis_kelamin' => $request->jenis_kelamin_2,
                        'tanggal_daftar' => $tanggalMulai,
                        'tanggal_selesai' => $tanggalSelesai,
                        'paket_id' => $savePaketId,
                        'status' => 'active',
                        'partner_id' => $idMember1,
                    ]);

                    $member1->partner_id = $idMember2;
                    $member1->save();

                    $namaPartnerStr = ' w/ ' . $member2->nama_lengkap;
                } else {
                    throw new \Exception('Untuk paket Couple, Anda wajib memilih pasangan terdaftar ATAU mengisi data pasangan baru.');
                }
            }

            // 3. UPLOAD BUKTI TRANSFER & INVOICE
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer') && !$skipInvoice) {
                $buktiPath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
            }

            $prefixInvoice = $skipInvoice ? 'BYP/FRN/' : 'INV/FRN/';
            $nomorInvoice = $prefixInvoice . now()->format('dmY') . '/' . strtoupper(Str::random(4));

            $keterangan = (str_contains(strtolower($paket->jenis), 'promo') ? 'Promo: ' : 'Reguler: ') . $namaPaket;
            if ($isCouple) {
                $keterangan .= $namaPartnerStr;
            }

            if ($skipInvoice) {
                $metodePembayaran = 'bypass';
                $nominalBayar = 0;
                $keterangan .= ' - BYPASS OWNER (Tanpa Bayar)';
            } else {
                $metodePembayaran = $buktiPath ? 'transfer' : 'cash';
                $nominalBayar = $hargaPaket;
                $keterangan .= ($buktiPath ? ' - Transfer' : ' - Tunai');
            }

            $payment = MembershipPayment::create([
                'member_id' => $member1->id_members,
                'paket_id' => $savePaketId,
                'jenis_transaksi' => 'registrasi',
                'nomor_invoice' => $nomorInvoice,
                'tanggal_transaksi' => now(),
                'metode_pembayaran' => $metodePembayaran,
                'nominal' => $nominalBayar,
                'status_pembayaran' => 'completed',
                'admin_id' => Auth::id() ?? 1,
                'keterangan' => $keterangan,
                'bukti_transfer' => $buktiPath,
            ]);

            if (!$skipInvoice) {
                try {
                    $this->kirimInvoiceTextWA($member1, $payment, $namaPaket, $tanggalSelesai);
                } catch (\Exception $e_wa) {
                    Log::error('WA Error: ' . $e_wa->getMessage());
                }
            }

            DB::commit();

            $successMsg = $skipInvoice ? 'Pendaftaran berhasil dibuat TANPA INVOICE (Akses Owner).' : 'Pendaftaran berhasil! Invoice telah dikirim ke WhatsApp.';
            return redirect()->back()->with('success', $successMsg);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error Sistem: ' . $e->getMessage())->withInput();
        }
    }
    
    private function kirimInvoiceTextWA($member, $payment, $namaPaket, $tglSelesai)
    {
        $no = preg_replace('/[^0-9]/', '', $member->no_telepon);
        if (substr($no, 0, 2) == '08') {
            $no = '628' . substr($no, 2);
        }
    
        if (!empty($no) && substr($no, 0, 2) == '62') {
    
            $linkAbsen = "https://furiongymjambi.com/dashboard-member?member_id=" . $member->id_members;
    
            $pesan =
                "🧾 *INVOICE PEMBAYARAN* 🧾\n" .
                "*FURION GYM JAMBI*\n" .
                "▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬\n\n" .
    
                "👤 *MEMBER INFO*\n" .
                "Nama   : {$member->nama_lengkap}\n" .
                "ID     : {$member->id_members}\n" .
                "No. HP : {$member->no_telepon}\n\n" .
    
                "📦 *DETAIL PAKET*\n" .
                "Paket   : {$namaPaket}\n" .
                "Expired : " . $tglSelesai->format('d M Y') . "\n\n" .
    
                "💰 *PEMBAYARAN*\n" .
                "No. Inv : {$payment->nomor_invoice}\n" .
                "Tgl     : " . now()->format('d/m/Y H:i') . "\n" .
                "*Total  : Rp " . number_format($payment->nominal, 0, ',', '.') . "*\n" .
                "Status  : ✅ LUNAS\n\n" .
    
                "🔗 *ABSEN MEMBER*\n" .
                "Member Furion bisa melihat Absen kehadirannya, berikut link untuk absen:\n" .
                $linkAbsen . "\n\n" .
    
                "▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬\n" .
                "Pesan ini adalah bukti pembayaran yang sah.\n" .
                "*Terima kasih & Selamat Latihan!* 💪🔥";
    
            Http::withoutVerifying()
                ->withHeaders(['Authorization' => env('FONNTE_TOKEN')])
                ->post('https://api.fonnte.com/send', [
                    'target' => $no,
                    'message' => $pesan,
                    'delay' => (string) rand(15, 30),
                    'countryCode' => '62',
                ]);
        }
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Members::with([
                'paket',
                'promo',
                'membershipPayments' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                }
            ])
                ->select('members.*')
                ->latest('updated_at');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_lengkap', function ($row) {
                    $initials = strtoupper(substr($row->nama_lengkap, 0, 2));
                    return '
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-9 w-9"> <div class="h-9 w-9 rounded-full bg-blue-100 border border-blue-200 flex items-center justify-center">
                                <span class="text-xs font-bold text-blue-600">' . $initials . '</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-semibold text-gray-800">' . $row->nama_lengkap . '</div>
                            <div class="text-[11px] text-gray-500 font-medium">ID: <span class="tracking-wide text-gray-400">' . $row->id_members . '</span></div>
                        </div>
                    </div>';
                })
                ->addColumn('paket_members', function ($row) {
                    $latestPayment = $row->membershipPayments->first();
                    $isBypass = $latestPayment && strtolower($latestPayment->metode_pembayaran) == 'bypass';

                    $bypassBadge = $isBypass ? '<span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black bg-red-100 text-red-600 border border-red-200">BYPASS</span>' : '';

                    if ($row->paket)
                        return '<span class="text-blue-600 font-medium">' . $row->paket->nama_paket . '</span>' . $bypassBadge;
                    elseif ($row->promo)
                        return '<span class="text-purple-600 font-medium">' . $row->promo->nama_paket . ' <span class="text-xs bg-purple-100 px-1 rounded">PROMO</span></span>' . $bypassBadge;
                    return '<span class="text-gray-400">-</span>';
                })
                ->addColumn('sisa_waktu', function ($row) {
                    $formattedDate = $row->tanggal_selesai ? date('d M Y', strtotime($row->tanggal_selesai)) : '-';
                    if (!$row->tanggal_selesai || $row->status == 'inactive') {
                        return '
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-xs font-bold text-gray-700 tracking-wide">' . $formattedDate . '</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-gray-500 border border-red-200">Masa Aktif Habis</span>
                        </div>';
                    }

                    $sisaHari = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($row->tanggal_selesai), false);
                    $sisaHari = ceil($sisaHari);

                    if ($sisaHari < 0) {
                        $badgeClass = 'bg-red-50 text-red-600 border-red-100';
                        $badgeText = 'Expired';
                        $icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    } elseif ($sisaHari <= 3) {
                        $badgeClass = 'bg-red-50 text-red-600 border-red-100';
                        $badgeText = $sisaHari . ' Hari Lagi';
                        $icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    } else {
                        $badgeClass = 'bg-green-50 text-green-600 border-green-100';
                        $badgeText = $sisaHari . ' Hari Lagi';
                        $icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    }

                    return '
                    <div class="flex flex-col items-start gap-1">
                        <span class="text-xs font-bold text-gray-700 tracking-wide">' . $formattedDate . '</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border ' . $badgeClass . '">' . $icon . $badgeText . '</span>
                    </div>';
                })
                ->addColumn('aksi', function ($row) {
                    $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    $deleteUrl = route('deleteMember', $row->id_members);
                    $csrf = csrf_token();
                    $separator = '<span class="text-gray-300 mx-1">|</span>';

                    $detailButton = '<button onclick="openDetailModal(' . $jsonData . ')" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm bg-transparent border-0 p-0 cursor-pointer transition-colors">Detail</button>' . $separator;

                    $actionButton = '';
                    if ($row->status == 'active') {
                        $actionButton = '<button onclick="openPerpanjangModal(' . $jsonData . ')" class="text-green-600 hover:text-green-800 font-medium text-sm bg-transparent border-0 p-0 cursor-pointer transition-colors">Perpanjang</button>' . $separator;
                    } elseif ($row->status == 'inactive') {
                        $actionButton = '<button onclick="openReaktifasiModal(' . $jsonData . ')" class="text-yellow-600 hover:text-yellow-800 font-medium text-sm bg-transparent border-0 p-0 cursor-pointer transition-colors">Re-aktifasi</button>' . $separator;
                    }

                    $editButton = '<button onclick="openEditModal(' . $jsonData . ')" class="text-blue-600 hover:text-blue-800 font-medium text-sm bg-transparent border-0 p-0 cursor-pointer transition-colors">Edit</button>';
                    $deleteForm = '<form action="' . $deleteUrl . '" method="POST" style="display:inline-block;"><input type="hidden" name="_token" value="' . $csrf . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" onclick="return confirm(\'Yakin ingin menghapus data ini?\')" class="text-red-600 hover:text-red-800 font-medium text-sm bg-transparent border-0 p-0 cursor-pointer transition-colors">Hapus</button></form>';

                    return '<div class="flex justify-end items-center gap-1">' . $detailButton . $actionButton . $editButton . $separator . $deleteForm . '</div>';
                })
                ->editColumn('tanggal_daftar', function ($row) {
                    return $row->tanggal_daftar ? date('d M Y', strtotime($row->tanggal_daftar)) : '-';
                })
                ->editColumn('tanggal_selesai', function ($row) {
                    return $row->tanggal_selesai ? date('d M Y', strtotime($row->tanggal_selesai)) : '-';
                })
                ->rawColumns(['aksi', 'status', 'sisa_waktu', 'nama_lengkap', 'paket_members'])
                ->make(true);
        }
    }

    public function deleteMember($id)
    {
        try {
            $member = Members::findOrFail($id);
            $member->delete();
            return redirect()->back()->with('success', 'Member berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function editMember(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama_member' => 'required|string|max:255',
                'nomor_telepon' => 'required|string|max:20',
                'email' => 'required|email|unique:members,email,' . $id . ',id_members',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            ]);

            $member = Members::findOrFail($id);
            $member->update([
                'nama_lengkap' => $validated['nama_member'],
                'no_telepon' => $validated['nomor_telepon'],
                'email' => $validated['email'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
            ]);

            return redirect()->back()->with('success', 'Data member berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function perpanjangMember(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id_paket' => 'required|exists:paket_members,id_paket',
                'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
            ]);

            $skipInvoice = $request->has('skip_invoice');
            if ($skipInvoice) {
                $ownerPassword = env('OWNER_PASSWORD', 'admin123');
                if ($request->owner_password !== $ownerPassword) {
                    return redirect()->back()->with('error', 'Password Owner Salah! Tidak diizinkan melewati pembuatan invoice.')->withInput();
                }
            }

            $paket = PaketMember::findOrFail($request->id_paket);
            $member = Members::findOrFail($id);

            $isCouple = (in_array($paket->jenis, ['couple', 'promo couple']) || stripos($paket->nama_paket, 'couple') !== false);

            if ($isCouple) {
                if (!$request->filled('partner_id') && !$request->filled('nama_member_2')) {
                    return redirect()->back()->with('error', 'Wajib memilih Pasangan terdaftar atau mengisi data Pasangan Baru untuk paket Couple.');
                }
                
                if ($request->filled('partner_id') && $request->partner_id == $member->id_members) {
                    return redirect()->back()->with('error', 'Pasangan tidak boleh diri sendiri.');
                }
            }

            $hitungTanggalBaru = function ($tanggalSelesaiLama, $durasiPaket) {
                $now = Carbon::now();
                $baseDate = $now;

                if ($tanggalSelesaiLama && Carbon::parse($tanggalSelesaiLama)->greaterThan($now)) {
                    $baseDate = Carbon::parse($tanggalSelesaiLama);
                }

                $angka = (int) filter_var($durasiPaket, FILTER_SANITIZE_NUMBER_INT);
                $durasiStr = strtolower($durasiPaket);
                $newDate = $baseDate->copy();

                if (str_contains($durasiStr, 'tahun'))
                    $newDate->addYears($angka);
                elseif (str_contains($durasiStr, 'hari'))
                    $newDate->addDays($angka);
                else
                    $newDate->addMonths($angka);

                return $newDate;
            };

            $generateId = function () {
                do {
                    $newId = 'FRN-' . mt_rand(100000, 999999);
                } while (Members::where('id_members', $newId)->exists());
                return $newId;
            };

            // UPDATE MEMBER UTAMA
            $tanggalSelesaiBaruMember = $hitungTanggalBaru($member->tanggal_selesai, $paket->durasi);
            $updateDataMember = [
                'paket_id' => $paket->id_paket,
                'tanggal_selesai' => $tanggalSelesaiBaruMember,
                'status' => 'active',
            ];

            // UPDATE PARTNER (JIKA COUPLE)
            $namaPartner = '';
            if ($isCouple) {
                if ($request->filled('partner_id')) {
                    $partner = Members::findOrFail($request->partner_id);
                    $tanggalSelesaiBaruPartner = $hitungTanggalBaru($partner->tanggal_selesai, $paket->durasi);

                    $partner->update([
                        'paket_id' => $paket->id_paket,
                        'tanggal_selesai' => $tanggalSelesaiBaruPartner,
                        'status' => 'active',
                        'partner_id' => $member->id_members,
                    ]);

                    $updateDataMember['partner_id'] = $request->partner_id;
                    $namaPartner = ' & ' . $partner->nama_lengkap;
                    
                } elseif ($request->filled('nama_member_2')) {
                    $idMember2 = $generateId();
                    $partner = Members::create([
                        'id_members' => $idMember2,
                        'nama_lengkap' => $request->nama_member_2,
                        'alamat' => '-',
                        'no_telepon' => $request->nomor_telepon_2,
                        'email' => $request->email_2,
                        'jenis_kelamin' => $request->jenis_kelamin_2,
                        'tanggal_daftar' => now(), // Join hari ini
                        'tanggal_selesai' => $tanggalSelesaiBaruMember, // Ikut tanggal selesai utama
                        'paket_id' => $paket->id_paket,
                        'status' => 'active',
                        'partner_id' => $member->id_members,
                    ]);

                    $updateDataMember['partner_id'] = $idMember2;
                    $namaPartner = ' & ' . $partner->nama_lengkap;
                }
            }
            
            $member->update($updateDataMember);

            // UPLOAD BUKTI TRANSFER
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer') && !$skipInvoice) {
                $buktiPath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
            }

            $prefixInvoice = $skipInvoice ? 'BYP-REN-' : 'INV-REN-';
            $nomorInvoice = $prefixInvoice . strtoupper(Str::random(6)) . '-' . now()->format('dmYHis');

            $keterangan = 'Perpanjang: ' . $paket->nama_paket . $namaPartner;

            if ($skipInvoice) {
                $metodePembayaran = 'bypass';
                $nominalBayar = 0;
                $keterangan .= ' - BYPASS OWNER';
            } else {
                $metodePembayaran = $buktiPath ? 'transfer' : 'cash';
                $nominalBayar = $paket->harga ?? 0;
                $keterangan .= ($buktiPath ? ' (Transfer)' : ' (Tunai)');
            }

            $payment = MembershipPayment::create([
                'member_id' => $member->id_members,
                'paket_id' => $paket->id_paket,
                'jenis_transaksi' => 'perpanjang', // Sesuai instruksi User Correct History
                'nomor_invoice' => $nomorInvoice,
                'tanggal_transaksi' => now(),
                'metode_pembayaran' => $metodePembayaran,
                'nominal' => $nominalBayar,
                'status_pembayaran' => 'completed',
                'admin_id' => Auth::id() ?? 1,
                'keterangan' => $keterangan,
                'bukti_transfer' => $buktiPath,
            ]);

            if (!$skipInvoice) {
                try {
                    $target = preg_replace('/[^0-9]/', '', $member->no_telepon);
                    if (substr($target, 0, 2) == '08')
                        $target = '628' . substr($target, 2);

                    $linkAbsen = "https://furiongymjambi.com/dashboard-member?member_id=" . $member->id_members;

                    $pesan = "Halo *{$member->nama_lengkap}*,\n\n" .
                        "Terima kasih telah memperpanjang keanggotaan di *Furion Gym*.\n" .
                        "Perpanjangan Berhasil! ✅\n\n" .
                        "*Rincian Perpanjangan:*\n" .
                        "--------------------------------\n" .
                        "🧾 Invoice: {$nomorInvoice}\n" .
                        "📦 Paket: {$paket->nama_paket}\n" .
                        "📅 Aktif Sampai: " . $tanggalSelesaiBaruMember->format('d M Y') . "\n" .
                        "💰 Total: Rp " . number_format($payment->nominal, 0, ',', '.') . "\n";

                    if (!empty($namaPartner)) {
                        $pesan .= "👥 Pasangan: " . trim($namaPartner, ' & ') . "\n";
                    }

                    $pesan .= "--------------------------------\n\n" .
                        "🔗 *ABSEN MEMBER*\n" .
                        "Berikut link untuk absen kehadiran Anda:\n" .
                        $linkAbsen . "\n\n" .
                        "Simpan pesan ini sebagai bukti pembayaran. Selamat Latihan! 💪";

                    if (!empty($target) && substr($target, 0, 2) == '62') {
                        Http::withoutVerifying()
                            ->withHeaders(['Authorization' => env('FONNTE_TOKEN')])
                            ->post('https://api.fonnte.com/send', [
                                'target' => $target,
                                'message' => $pesan,
                                'countryCode' => '62',
                                'delay' => (string) rand(15, 30),
                            ]);
                    }
                } catch (\Exception $e_wa) {
                    Log::error("Gagal kirim WA Perpanjangan: " . $e_wa->getMessage());
                }
            }

            DB::commit();

            if ($skipInvoice) {
                $msgSuccess = "Perpanjangan berhasil TANPA INVOICE. Aktif s/d " . $tanggalSelesaiBaruMember->format('d M Y');
            } else {
                $msgSuccess = 'Perpanjangan berhasil! Member utama s/d ' . $tanggalSelesaiBaruMember->format('d M Y');
            }

            if (!empty($namaPartner))
                $msgSuccess .= ". Pasangan diperpanjang otomatis.";

            return redirect()->back()->with('success', $msgSuccess);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function reaktivasiMember(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'paket_id' => 'required|exists:paket_members,id_paket',
                'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
            ]);

            $skipInvoice = $request->has('skip_invoice');
            if ($skipInvoice) {
                $ownerPassword = env('OWNER_PASSWORD', 'admin123');
                if ($request->owner_password !== $ownerPassword) {
                    return redirect()->back()->with('error', 'Password Owner Salah! Tidak diizinkan melewati pembuatan invoice.')->withInput();
                }
            }

            $paket = PaketMember::findOrFail($request->paket_id);
            $member = Members::findOrFail($id);

            $isCouple = (in_array($paket->jenis, ['couple', 'promo couple']) || stripos($paket->nama_paket, 'couple') !== false);

            if ($isCouple) {
                if (!$request->filled('partner_id') && !$request->filled('nama_member_2')) {
                    return redirect()->back()->with('error', 'Wajib memilih Pasangan terdaftar atau mengisi data Pasangan Baru untuk paket Couple.');
                }

                if ($request->filled('partner_id') && $request->partner_id == $member->id_members) {
                    return redirect()->back()->with('error', 'Pasangan tidak boleh diri sendiri.');
                }
            }

            $tanggalMulai = Carbon::now();
            $angka = (int) filter_var($paket->durasi, FILTER_SANITIZE_NUMBER_INT);
            $durasiStr = strtolower($paket->durasi);
            $tanggalSelesai = $tanggalMulai->copy();

            if (str_contains($durasiStr, 'tahun'))
                $tanggalSelesai->addYears($angka);
            elseif (str_contains($durasiStr, 'hari'))
                $tanggalSelesai->addDays($angka);
            else
                $tanggalSelesai->addMonths($angka);

            $generateId = function () {
                do {
                    $newId = 'FRN-' . mt_rand(100000, 999999);
                } while (Members::where('id_members', $newId)->exists());
                return $newId;
            };

            // UPDATE MEMBER UTAMA
            $updateDataMember = [
                'paket_id' => $paket->id_paket,
                'tanggal_selesai' => $tanggalSelesai,
                'status' => 'active',
            ];

            // UPDATE PARTNER (JIKA COUPLE)
            $namaPartner = '';
            if ($isCouple) {
                if ($request->filled('partner_id')) {
                    $partner = Members::findOrFail($request->partner_id);
                    $partner->update([
                        'paket_id' => $paket->id_paket,
                        'tanggal_selesai' => $tanggalSelesai,
                        'status' => 'active',
                        'partner_id' => $member->id_members,
                    ]);

                    $updateDataMember['partner_id'] = $partner->id_members;
                    $namaPartner = ' & ' . $partner->nama_lengkap;
                } elseif ($request->filled('nama_member_2')) {
                    $idMember2 = $generateId();
                    $partner = Members::create([
                        'id_members' => $idMember2,
                        'nama_lengkap' => $request->nama_member_2,
                        'alamat' => '-',
                        'no_telepon' => $request->nomor_telepon_2,
                        'email' => $request->email_2,
                        'jenis_kelamin' => $request->jenis_kelamin_2,
                        'tanggal_daftar' => now(), // Join hari ini
                        'tanggal_selesai' => $tanggalSelesai, // Ikut tanggal selesai utama
                        'paket_id' => $paket->id_paket,
                        'status' => 'active',
                        'partner_id' => $member->id_members,
                    ]);

                    $updateDataMember['partner_id'] = $idMember2;
                    $namaPartner = ' & ' . $partner->nama_lengkap;
                }
            }

            $member->update($updateDataMember);

            // UPLOAD BUKTI TRANSFER
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer') && !$skipInvoice) {
                $buktiPath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
            }

            $prefixInvoice = $skipInvoice ? 'BYP-RCT-' : 'INV-RCT-';
            $nomorInvoice = $prefixInvoice . strtoupper(Str::random(6)) . '-' . now()->format('dmYHis');

            $keterangan = 'Re-aktifasi: ' . $paket->nama_paket . $namaPartner;

            if ($skipInvoice) {
                $metodePembayaran = 'bypass';
                $nominalBayar = 0;
                $keterangan .= ' - BYPASS OWNER';
            } else {
                $metodePembayaran = $buktiPath ? 'transfer' : 'cash';
                $nominalBayar = $paket->harga ?? 0;
                $keterangan .= ($buktiPath ? ' (Transfer)' : ' (Tunai)');
            }

            $payment = MembershipPayment::create([
                'member_id' => $member->id_members,
                'paket_id' => $paket->id_paket,
                'jenis_transaksi' => 'reaktivasi', // Sesuai instruksi User Correct History
                'nomor_invoice' => $nomorInvoice,
                'tanggal_transaksi' => now(),
                'metode_pembayaran' => $metodePembayaran,
                'nominal' => $nominalBayar,
                'status_pembayaran' => 'completed',
                'admin_id' => Auth::id() ?? 1,
                'keterangan' => $keterangan,
                'bukti_transfer' => $buktiPath,
            ]);

            if (!$skipInvoice) {
                try {
                    $target = preg_replace('/[^0-9]/', '', $member->no_telepon);
                    if (substr($target, 0, 2) == '08')
                        $target = '628' . substr($target, 2);

                    $linkAbsen = "https://furiongymjambi.com/dashboard-member?member_id=" . $member->id_members;

                    $pesan = "Halo *{$member->nama_lengkap}*! 👋\n\n" .
                        "Selamat datang kembali di *Furion Gym*.\n" .
                        "Akun Anda berhasil diaktifkan! ✅\n\n" .
                        "*Rincian Re-Aktivasi:*\n" .
                        "--------------------------------\n" .
                        "🧾 Invoice: {$nomorInvoice}\n" .
                        "📦 Paket: {$paket->nama_paket}\n" .
                        "📅 Aktif Sampai: " . $tanggalSelesai->format('d M Y') . "\n" .
                        "💰 Total Bayar: Rp " . number_format($payment->nominal, 0, ',', '.') . "\n";

                    if (!empty($namaPartner)) {
                        $pesan .= "👥 Pasangan: " . trim($namaPartner, ' & ') . "\n";
                    }

                    $pesan .= "--------------------------------\n\n" .
                        "🔗 *ABSEN MEMBER*\n" .
                        "Berikut link untuk absen kehadiran Anda:\n" .
                        $linkAbsen . "\n\n" .
                        "Simpan pesan ini sebagai bukti pembayaran. Selamat Latihan! 💪";

                    if (!empty($target) && substr($target, 0, 2) == '62') {
                        Http::withoutVerifying()
                            ->withHeaders(['Authorization' => env('FONNTE_TOKEN')])
                            ->post('https://api.fonnte.com/send', [
                                'target' => $target,
                                'message' => $pesan,
                                'countryCode' => '62',
                                'delay' => (string) rand(15, 30),
                            ]);
                    }
                } catch (\Exception $e_wa) {
                    Log::error("Gagal kirim WA Reaktivasi: " . $e_wa->getMessage());
                }
            }

            DB::commit();

            if ($skipInvoice) {
                return redirect()->back()->with('success', 'Member diaktifkan TANPA INVOICE! Aktif hingga ' . $tanggalSelesai->format('d M Y') . '.');
            } else {
                return redirect()->back()->with('success', 'Member berhasil diaktifkan kembali! Aktif hingga ' . $tanggalSelesai->format('d M Y') . '.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat re-aktifasi: ' . $e->getMessage());
        }
    }
}