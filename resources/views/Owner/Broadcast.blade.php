@extends('Owner.OwnerTemplate')

@section('title', '')

@section('header-content')
<div class="flex flex-col md:flex-row justify-between items-center gap-4">
    <div>
        <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Broadcast Reminder</h2>
        <p class="text-gray-500 text-sm mt-1">Sistem deteksi otomatis member yang akan habis masa aktifnya.</p>
    </div>
    <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        Hari ini: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
    </div>
</div>
@endsection

@section('content')
<!-- Contoh Tabel Log di Blade -->
<div class="mt-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Riwayat & Status Broadcast</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">Level</th>
                    <th class="px-4 py-3">Pesan Log</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap" title="{{ $log['raw_date'] }}">
                        {{ $log['date'] }}
                    </td>
                    <td class="px-4 py-3">
                        @if($log['level'] == 'ERROR')
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">ERROR</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">INFO</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-700">
                        {{ Str::limit($log['message'], 100) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-3 text-center text-gray-400">Belum ada log aktivitas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection