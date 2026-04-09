<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard {{ $member->nama_lengkap }} - Furion Gym</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Teko:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .brand-font {
            font-family: 'Teko', sans-serif;
            letter-spacing: 0.05em;
        }

        .bg-brand-blue {
            background-color: #0026e6;
        }

        .text-brand-blue {
            color: #0026e6;
        }

        .text-brand-yellow {
            color: #fbcc16;
        }

        .bg-brand-yellow {
            background-color: #fbcc16;
        }

        /* Story Modal Styles */
        .story-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(10px);
        }

        .story-modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        .story-content {
            width: 414px;
            height: 736px;
            background: linear-gradient(180deg, #2F4FD8 0%, #1E3BA0 100%);
            border-radius: 40px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .story-modal.active .story-content {
            animation: scaleUp 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleUp {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Utility */
        .force-render * {
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
@if(session('success'))
    <div id="toast-success"
        class="fixed top-20 right-5 z-[100] flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-lg border-l-4 border-green-500 animate-bounce"
        role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
            <i data-lucide="check" class="w-5 h-5"></i>
        </div>
        <div class="ml-3 text-sm font-normal text-slate-800">{{ session('success') }}</div>
    </div>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-success');
            if (toast) toast.style.display = 'none';
        }, 3000);
    </script>
@endif

<body class="bg-slate-50 text-slate-800 min-h-screen">

    <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-slate-500 hover:text-brand-blue transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i> <span class="hidden sm:inline">Kembali</span>
            </a>
            <div class="flex items-center gap-2">
                <i data-lucide="zap" class="text-brand-yellow fill-current w-5 h-5"></i>
                <span class="brand-font text-2xl font-bold text-slate-900">FURION <span
                        class="text-brand-blue">DASHBOARD</span></span>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Laporan Aktivitas</p>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-1">Halo, <span
                        class="text-brand-blue">{{ $member->nama_lengkap }}</span> 👋</h1>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-end sm:items-center gap-3">

                <button onclick="openHistoryModal()"
                    class="flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 px-4 py-3 sm:py-2.5 rounded-lg border border-slate-200 shadow-sm transition-all font-bold text-sm hover:border-blue-200 hover:text-brand-blue active:scale-95">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                    <span>Riwayat Transaksi</span>
                </button>

                <div class="bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 uppercase font-bold">ID Member</p>
                        <p class="font-mono font-bold text-lg text-slate-800 tracking-wider">{{ $member->id_members }}
                        </p>
                    </div>
                    <div class="bg-blue-50 p-2 rounded text-brand-blue">
                        <i data-lucide="qr-code" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1 space-y-6">

                <div
                    class="bg-white rounded-2xl shadow-lg border-t-4 {{ $remainingDays > 0 ? 'border-brand-yellow' : 'border-red-500' }} p-6 relative overflow-hidden">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <p class="text-xs text-slate-400 uppercase tracking-widest font-bold">Status Paket</p>
                            @if($remainingDays > 0)
                                <span
                                    class="inline-flex items-center gap-1 mt-1 px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-bold border border-green-200">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i> ACTIVE
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 mt-1 px-2 py-1 rounded bg-red-100 text-red-700 text-xs font-bold border border-red-200">
                                    <i data-lucide="x-circle" class="w-3 h-3"></i> EXPIRED
                                </span>
                            @endif
                        </div>
                        <i data-lucide="crown" class="w-10 h-10 text-slate-100"></i>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-slate-500">Jenis Paket</p>
                            <p class="text-xl font-bold text-slate-900">{{ $member->nama_paket_aktif }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Berlaku Hingga</p>
                            <p class="text-xl font-bold {{ $remainingDays < 7 ? 'text-red-600' : 'text-slate-800' }}">
                                {{ \Carbon\Carbon::parse($member->tanggal_selesai)->format('d M Y') }}
                            </p>
                            @if($remainingDays > 0)
                                <p class="text-xs text-slate-400 mt-1">Sisa {{ $remainingDays }} Hari Lagi</p>
                            @else
                                <p class="text-xs text-red-500 mt-1 font-bold">Paket Telah Berakhir!</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div
                    class="bg-brand-blue rounded-2xl shadow-lg p-6 text-white relative overflow-hidden group transition-all hover:shadow-blue-900/20">

                    <button onclick="openTargetModal()"
                        class="absolute top-4 left-4 z-20 bg-black/20 hover:bg-black/40 backdrop-blur-md border border-white/10 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition cursor-pointer text-blue-100 hover:text-white">
                        <i data-lucide="settings-2" class="w-4 h-4"></i>
                        Target: {{ ucfirst($level) }}
                    </button>

                    <a href="{{ route('member.story.download', ['member_id' => $member->id_members, 'month' => $currentMonth->month, 'year' => $currentMonth->year]) }}"
                        id="downloadStoryBtn" onclick="startDownload(event, this.href)"
                        class="absolute top-4 right-4 z-20 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition cursor-pointer text-white no-underline group">
                        <i data-lucide="instagram" class="w-4 h-4 group-hover:scale-110 transition"></i>
                        Share
                    </a>

                    <i data-lucide="trending-up"
                        class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10 group-hover:scale-110 transition duration-500"></i>

                    <div class="mt-8 relative z-10">
                        <h3 class="font-bold text-lg">Evaluasi Bulan Ini</h3>
                        <p class="text-blue-200 text-xs mb-6 uppercase tracking-widest">
                            {{ $currentMonth->translatedFormat('F Y') }}
                        </p>

                        <div class="flex items-end gap-2 mb-2">
                            <span class="text-5xl font-bold font-mono brand-font">{{ $displayPercent }}<span
                                    class="text-3xl">%</span></span>
                            <span class="text-blue-200 text-sm mb-2 font-medium">Konsistensi</span>
                        </div>

                        <div class="w-full bg-blue-900/50 rounded-full h-2 mb-2 overflow-hidden">
                            <div class="{{ $feedbackColor }} h-2 rounded-full transition-all duration-1000 shadow-[0_0_10px_currentColor]"
                                style="width: {{ $progressPercent }}%"></div>
                        </div>

                        <div class="flex justify-between text-[10px] text-blue-200 mb-4 font-mono">
                            <span>0%</span>
                            <span>Target: {{ $totalSessions }} / {{ $targetSessions }} Sesi</span>
                            <span>100%</span>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 border border-white/10 shadow-inner">
                            <p class="text-sm font-medium text-white italic">"{{ $feedback }}"</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">

                    <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
                        <h3 class="font-bold text-slate-800 text-xl flex items-center gap-2">
                            <i data-lucide="calendar" class="w-5 h-5 text-brand-blue"></i>
                            Jurnal Latihan
                        </h3>

                        <div class="flex items-center gap-4 bg-slate-100 p-1 rounded-lg">
                            <a href="?member_id={{ $member->id_members }}&month={{ $prevMonth->month }}&year={{ $prevMonth->year }}"
                                class="p-2 hover:bg-white hover:shadow-sm rounded-md transition text-slate-600">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </a>

                            <span class="font-bold text-slate-700 min-w-[120px] text-center capitalize">
                                {{ $currentMonth->translatedFormat('F Y') }}
                            </span>

                            <a href="?member_id={{ $member->id_members }}&month={{ $nextMonth->month }}&year={{ $nextMonth->year }}"
                                class="p-2 hover:bg-white hover:shadow-sm rounded-md transition text-slate-600">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-7 gap-2 md:gap-3 mb-8">
                        @foreach(['Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb', 'Mg'] as $day)
                            <div class="text-center text-xs text-slate-400 font-bold mb-2">{{ $day }}</div>
                        @endforeach

                        @for($i = 0; $i < $currentMonth->copy()->startOfMonth()->dayOfWeekIso - 1; $i++)
                            <div></div>
                        @endfor

                        @for($day = 1; $day <= $currentMonth->daysInMonth; $day++)
                            @php $isTrained = in_array($day, $trainingDates); @endphp
                            <div
                                class="aspect-square rounded-lg flex items-center justify-center text-sm font-bold transition duration-300 relative group cursor-default
                                                            {{ $isTrained ? 'bg-green-500 text-white shadow-md shadow-green-200 scale-105' : 'bg-slate-50 text-slate-300' }}">
                                {{ $day }}
                            </div>
                        @endfor
                    </div>

                    <div class="border-t border-slate-100 pt-8">
                        <h4 class="text-sm font-bold text-slate-900 mb-4">Riwayat Terakhir</h4>
                        @if($monthlyAttendances->count() > 0)
                            <div class="overflow-hidden rounded-lg border border-slate-200">
                                <table class="w-full text-left">
                                    <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                                        <tr>
                                            <th class="p-4">Tanggal</th>
                                            <th class="p-4">Jam</th>
                                            <th class="p-4 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm text-slate-600 divide-y divide-slate-100">
                                        @foreach($monthlyAttendances as $log)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="p-4 font-bold">
                                                    {{ \Carbon\Carbon::parse($log->waktu_masuk)->translatedFormat('d F Y') }}
                                                </td>
                                                <td class="p-4 font-mono">
                                                    {{ \Carbon\Carbon::parse($log->waktu_masuk)->format('H:i') }}
                                                </td>
                                                <td class="p-4 text-right">
                                                    <span
                                                        class="bg-blue-100 text-brand-blue px-2 py-1 rounded text-xs font-bold">Hadir</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 bg-slate-50 rounded-lg border border-dashed border-slate-200">
                                <p class="text-slate-400 text-sm">Belum ada latihan bulan ini. Yuk gas!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="targetModal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity opacity-0 cursor-pointer"
            id="targetBackdrop" onclick="closeTargetModal()"></div>

        <div class="fixed inset-0 z-[101] overflow-y-auto pointer-events-none">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                <div id="targetPanel"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all opacity-0 scale-95 sm:w-full sm:max-w-md pointer-events-auto">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i data-lucide="target" class="w-6 h-6 text-brand-blue"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-slate-900">Atur Target Latihan</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 mb-4">Pilih target yang sesuai dengan kesibukanmu.
                                    </p>

                                    <form action="{{ route('member.update.target') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="member_id" value="{{ $member->id_members }}">

                                        <div class="space-y-3">
                                            <label onclick="selectOption(this)"
                                                class="target-option relative flex cursor-pointer rounded-xl border p-4 shadow-sm hover:bg-blue-50 transition {{ ($level == 'beginner') ? 'border-brand-blue bg-blue-50 ring-1 ring-brand-blue' : 'border-slate-200' }}">
                                                <input type="radio" name="target_latihan" value="beginner"
                                                    class="sr-only" {{ ($level == 'beginner') ? 'checked' : '' }}>
                                                <div class="flex-1">
                                                    <span class="block text-sm font-bold text-slate-900">Beginner
                                                        (Pemula)</span>
                                                    <span class="text-xs text-slate-500">Target: 12 Sesi / Bulan</span>
                                                </div>
                                                <i data-lucide="check-circle"
                                                    class="check-icon h-5 w-5 text-brand-blue {{ ($level == 'beginner') ? '' : 'hidden' }}"></i>
                                            </label>

                                            <label onclick="selectOption(this)"
                                                class="target-option relative flex cursor-pointer rounded-xl border p-4 shadow-sm hover:bg-blue-50 transition {{ ($level == 'intermediate') ? 'border-brand-blue bg-blue-50 ring-1 ring-brand-blue' : 'border-slate-200' }}">
                                                <input type="radio" name="target_latihan" value="intermediate"
                                                    class="sr-only" {{ ($level == 'intermediate') ? 'checked' : '' }}>
                                                <div class="flex-1">
                                                    <span
                                                        class="block text-sm font-bold text-slate-900">Intermediate</span>
                                                    <span class="text-xs text-slate-500">Target: 16 Sesi / Bulan</span>
                                                </div>
                                                <i data-lucide="check-circle"
                                                    class="check-icon h-5 w-5 text-brand-blue {{ ($level == 'intermediate') ? '' : 'hidden' }}"></i>
                                            </label>

                                            <label onclick="selectOption(this)"
                                                class="target-option relative flex cursor-pointer rounded-xl border p-4 shadow-sm hover:bg-blue-50 transition {{ ($level == 'advance') ? 'border-brand-blue bg-blue-50 ring-1 ring-brand-blue' : 'border-slate-200' }}">
                                                <input type="radio" name="target_latihan" value="advance"
                                                    class="sr-only" {{ ($level == 'advance') ? 'checked' : '' }}>
                                                <div class="flex-1">
                                                    <span class="block text-sm font-bold text-slate-900">Advance
                                                        (Mahir)</span>
                                                    <span class="text-xs text-slate-500">Target: 20 Sesi / Bulan</span>
                                                </div>
                                                <i data-lucide="check-circle"
                                                    class="check-icon h-5 w-5 text-brand-blue {{ ($level == 'advance') ? '' : 'hidden' }}"></i>
                                            </label>
                                        </div>

                                        <div class="mt-6 sm:flex sm:flex-row-reverse gap-2">
                                            <button type="submit"
                                                class="w-full inline-flex justify-center rounded-lg bg-brand-blue px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-800 sm:w-auto">Simpan</button>
                                            <button type="button" onclick="closeTargetModal()"
                                                class="mt-3 w-full inline-flex justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingOverlay"
        class="fixed inset-0 z-[9999] bg-[#0f172a]/90 backdrop-blur-sm hidden flex-col items-center justify-center transition-all duration-300">
        <div class="flex flex-col items-center animate-pulse">
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-[#FBCC16] blur-xl opacity-50 rounded-full animate-ping"></div>
                <div class="relative bg-[#FBCC16] p-4 rounded-2xl shadow-2xl animate-bounce">
                    <i data-lucide="dumbbell" class="w-10 h-10 text-black fill-black"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-white italic tracking-wide mb-2 brand-font">GENERATING STORY...</h3>
            <p class="text-blue-200 text-sm font-medium animate-pulse">Mohon tunggu, sedang menyusun data latihanmu...
            </p>
        </div>
    </div>

    <div id="successOverlay"
        class="fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm hidden flex-col items-center justify-center transition-all duration-300">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full mx-4 text-center shadow-2xl transform scale-90 opacity-0 transition-all duration-300"
            id="successCard">
            <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
                <i data-lucide="check" class="w-10 h-10 text-green-600 stroke-[3]"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">Berhasil Disimpan!</h3>
            <p class="text-slate-500 text-sm mb-8 leading-relaxed">Story kamu siap diposting ke Instagram!</p>
            <button onclick="closeOverlay()"
                class="w-full bg-[#0026e6] hover:bg-blue-800 text-white font-bold py-3.5 rounded-xl transition shadow-lg hover:shadow-xl transform active:scale-95">Mantap!</button>
        </div>
    </div>

    <div class="story-modal force-render" id="storyModal">
        <div class="story-content" id="storyLayout">
        </div>
    </div>

    {{-- MODAL RIWAYAT TRANSAKSI --}}
    <div id="historyModal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">

        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity opacity-0 cursor-pointer"
            id="historyBackdrop" onclick="closeHistoryModal()"></div>

        <div class="fixed inset-0 z-[101] overflow-y-auto pointer-events-none">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                {{-- Modal Panel --}}
                <div id="historyPanel"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all opacity-0 scale-95 w-full sm:max-w-xl pointer-events-auto flex flex-col max-h-[85vh]">

                    {{-- Header Modal --}}
                    <div
                        class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center shrink-0">
                        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <div class="bg-blue-100 p-1.5 rounded-lg">
                                <i data-lucide="receipt" class="w-5 h-5 text-brand-blue"></i>
                            </div>
                            Riwayat Transaksi
                        </h3>
                        <div class="flex items-center gap-2">
                            {{-- TOMBOL DOWNLOAD PDF --}}
                            <a href="{{ route('member.riwayat.pdf', $member->id_members) }}" target="_blank"
                                class="flex items-center gap-1.5 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-lg transition-colors text-xs font-bold border border-red-100 hover:border-red-500">
                                <i data-lucide="file-down" class="w-4 h-4"></i>
                                <span class="hidden sm:inline">Download PDF</span>
                                <span class="sm:hidden">PDF</span>
                            </a>

                            <button onclick="closeHistoryModal()"
                                class="text-slate-400 hover:text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition-colors">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Body/Isi Modal (Scrollable) --}}
                    <div class="p-6 overflow-y-auto bg-white flex-1 custom-scrollbar">
                        <div class="space-y-4">

                            @forelse($riwayatTransaksi as $transaksi)
                                @php
                                    // Dynamic Styling based on Transaction Type
                                    $jenis = strtolower($transaksi->jenis_transaksi);
                                    if ($jenis == 'membership') {
                                        $badgeBg = 'bg-indigo-50';
                                        $badgeText = 'text-indigo-600';
                                        $labelJenis = 'Pendaftaran';
                                    } elseif ($jenis == 'renewal') {
                                        $badgeBg = 'bg-green-50';
                                        $badgeText = 'text-green-600';
                                        $labelJenis = 'Perpanjangan';
                                    } elseif ($jenis == 'reactivation') {
                                        $badgeBg = 'bg-orange-50';
                                        $badgeText = 'text-orange-600';
                                        $labelJenis = 'Reaktivasi';
                                    } else {
                                        $badgeBg = 'bg-blue-50';
                                        $badgeText = 'text-brand-blue';
                                        $labelJenis = $transaksi->jenis_transaksi;
                                    }

                                    $harga = $transaksi->nominal ?? ($transaksi->total_pembayaran ?? 0);
                                @endphp

                                <div
                                    class="border border-slate-100 rounded-xl p-4 hover:border-blue-200 hover:shadow-md transition-all group">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span
                                                class="inline-block {{ $badgeBg }} {{ $badgeText }} text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-1.5">
                                                {{ $labelJenis }}
                                            </span>
                                            <h4
                                                class="font-bold text-slate-800 text-sm group-hover:text-brand-blue transition-colors">
                                                {{ $transaksi->nama_paket_snapshot ?? ($transaksi->paket->nama_paket ?? 'Paket Kustom') }}
                                            </h4>
                                        </div>

                                        {{-- BAGIAN YANG DIUBAH: Harga + Tombol Struk --}}
                                        <div class="flex flex-col items-end gap-1.5 shrink-0">
                                            <span class="text-brand-blue font-black text-sm">Rp
                                                {{ number_format($harga, 0, ',', '.') }}</span>
                                            <a href="{{ route('member.transaksi.pdf', $transaksi->id) }}" target="_blank"
                                                class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white px-2 py-1 rounded text-[10px] font-bold border border-red-100 hover:border-red-500 transition-colors"
                                                title="Download Struk">
                                                <i data-lucide="printer" class="w-3 h-3"></i> Cetak Struk
                                            </a>
                                        </div>
                                        {{-- END BAGIAN YANG DIUBAH --}}

                                    </div>
                                    <div
                                        class="flex justify-between items-center text-xs text-slate-500 border-t border-slate-50 pt-3 mt-1">
                                        <div class="flex items-center gap-1.5 font-medium">
                                            <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                            {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y') }}
                                        </div>
                                        <span
                                            class="bg-green-100 text-green-700 px-2.5 py-1 rounded-md font-bold text-[10px] flex items-center gap-1">
                                            <i data-lucide="check-circle" class="w-3 h-3"></i> LUNAS
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div
                                        class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i data-lucide="receipt" class="w-8 h-8 text-slate-300"></i>
                                    </div>
                                    <h4 class="text-slate-500 font-medium text-sm">Belum ada riwayat transaksi.</h4>
                                </div>
                            @endforelse

                        </div>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end shrink-0">
                        <button onclick="closeHistoryModal()"
                            class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 active:scale-95 transition-all">Tutup</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        window.openTargetModal = function () {
            const modal = document.getElementById('targetModal');
            const backdrop = document.getElementById('targetBackdrop');
            const panel = document.getElementById('targetPanel');

            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    panel.classList.remove('opacity-0', 'scale-95');
                    panel.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                console.error('Modal element not found!');
            }
        };

        window.closeTargetModal = function () {
            const modal = document.getElementById('targetModal');
            const backdrop = document.getElementById('targetBackdrop');
            const panel = document.getElementById('targetPanel');

            if (modal) {
                backdrop.classList.add('opacity-0');
                panel.classList.remove('opacity-100', 'scale-100');
                panel.classList.add('opacity-0', 'scale-95');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        };

        window.selectOption = function (element) {
            document.querySelectorAll('.target-option').forEach((el) => {
                el.classList.remove('border-brand-blue', 'bg-blue-50', 'ring-1', 'ring-brand-blue');
                el.classList.add('border-slate-200');
                // Sembunyikan icon check
                const icon = el.querySelector('.check-icon');
                if (icon) icon.classList.add('hidden');
            });

            element.classList.remove('border-slate-200');
            element.classList.add('border-brand-blue', 'bg-blue-50', 'ring-1', 'ring-brand-blue');

            const selectedIcon = element.querySelector('.check-icon');
            if (selectedIcon) selectedIcon.classList.remove('hidden');

            // 4. Pastikan Radio Button di dalamnya terpilih (Checked)
            const radioInput = element.querySelector('input[type="radio"]');
            if (radioInput) radioInput.checked = true;
        };

        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const month = urlParams.get('month');
            const year = urlParams.get('year');
            const downloadBtn = document.getElementById('downloadStoryBtn');

            if (month && year && downloadBtn) {
                const currentHref = new URL(downloadBtn.href);
                currentHref.searchParams.set('month', month);
                currentHref.searchParams.set('year', year);
                downloadBtn.href = currentHref.toString();
            }
        });

        window.startDownload = function (e, url) {
            e.preventDefault();
            const loadingOverlay = document.getElementById('loadingOverlay');
            const successOverlay = document.getElementById('successOverlay');
            const successCard = document.getElementById('successCard');

            loadingOverlay.classList.remove('hidden');
            loadingOverlay.classList.add('flex');

            window.location.href = url;

            setTimeout(() => {
                loadingOverlay.classList.add('hidden');
                loadingOverlay.classList.remove('flex');
                successOverlay.classList.remove('hidden');
                successOverlay.classList.add('flex');
                setTimeout(() => {
                    successCard.classList.remove('scale-90', 'opacity-0');
                    successCard.classList.add('scale-100', 'opacity-100');
                    lucide.createIcons();
                }, 50);
            }, 5000);
        };

        window.closeOverlay = function () {
            const successOverlay = document.getElementById('successOverlay');
            const successCard = document.getElementById('successCard');
            successCard.classList.remove('scale-100', 'opacity-100');
            successCard.classList.add('scale-90', 'opacity-0');
            setTimeout(() => {
                successOverlay.classList.add('hidden');
                successOverlay.classList.remove('flex');
            }, 200);
        };

        // FUNGSI UNTUK MODAL RIWAYAT TRANSAKSI
        window.openHistoryModal = function () {
            const modal = document.getElementById('historyModal');
            const backdrop = document.getElementById('historyBackdrop');
            const panel = document.getElementById('historyPanel');

            if (modal) {
                modal.classList.remove('hidden');
                // Trigger re-render icons inside modal just in case
                lucide.createIcons();

                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    panel.classList.remove('opacity-0', 'scale-95');
                    panel.classList.add('opacity-100', 'scale-100');
                }, 10);
            }
        };

        window.closeHistoryModal = function () {
            const modal = document.getElementById('historyModal');
            const backdrop = document.getElementById('historyBackdrop');
            const panel = document.getElementById('historyPanel');

            if (modal) {
                backdrop.classList.add('opacity-0');
                panel.classList.remove('opacity-100', 'scale-100');
                panel.classList.add('opacity-0', 'scale-95');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        };
    </script>
</body>

</html>