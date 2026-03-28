<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Teko:ital,wght@0,300..700;1,300..700&family=Roboto:wght@400;500;700;900&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 414px;
            height: 736px;
            overflow: hidden;
            font-family: 'Roboto', sans-serif;
        }

        .brand-font {
            font-family: 'Teko', sans-serif;
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="bg-[#0f172a]">
    <div id="storyLayout" class="w-[414px] h-[736px] relative flex flex-col overflow-hidden bg-[#0f172a]">

        <div class="absolute inset-0 bg-gradient-to-b from-[#2F4FD8] via-[#1E3BA0] to-[#0f172a]"></div>

        <div class="relative z-10 h-full flex flex-col pt-6">
            <div class="flex flex-col items-center mb-4 px-6">
                <div class="flex items-center justify-center gap-3 mb-2">
                    <div class="relative bg-[#FBCC16] w-12 h-12 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="dumbbell" class="w-7 h-7 text-black fill-black"
                            style="transform: rotate(-45deg);"></i>
                    </div>
                    <div class="flex flex-col leading-[0.8]">
                        <h1 class="text-[2.8rem] font-black text-white italic brand-font tracking-wide">FURION</h1>
                        <h1 class="text-[2.8rem] font-black text-white italic brand-font tracking-wide">GYM</h1>
                        <h1 class="text-[2.8rem] font-black text-white italic brand-font tracking-wide">JAMBI</h1>
                    </div>
                </div>
                <div class="border border-[#FBCC16] px-6 py-0.5 rounded-full bg-[#FBCC16]/5 backdrop-blur-sm">
                    <span class="text-[#FBCC16] font-bold text-lg tracking-[0.1em] uppercase brand-font">
                        {{ strtoupper($currentMonth->translatedFormat('F Y')) }}
                    </span>
                </div>
            </div>

            <div class="px-5 mb-4">
                <div class="bg-[#1e293b]/40 backdrop-blur-xl rounded-[1.5rem] p-4 border border-white/5 shadow-2xl">
                    <div class="grid grid-cols-7 mb-2 text-center">
                        @foreach(['S', 'S', 'R', 'K', 'J', 'S', 'M'] as $d)
                            <div class="text-[10px] font-black text-blue-200/60">{{ $d }}</div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-7 gap-y-1 text-center">
                        @for($i = 0; $i < $currentMonth->copy()->startOfMonth()->dayOfWeekIso - 1; $i++)
                            <div></div>
                        @endfor
                        @for($day = 1; $day <= $currentMonth->daysInMonth; $day++)
                            <div class="flex items-center justify-center relative h-8">
                                @if(in_array($day, $trainingDates))
                                    <span
                                        class="relative z-10 text-xs font-bold w-7 h-7 flex items-center justify-center rounded-full bg-[#FBCC16] text-black shadow-lg">
                                        {{ $day }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-semibold text-slate-300/30">
                                        {{ $day }}
                                    </span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center gap-0 mb-4 px-6">
                <div class="flex-1 text-center border-r border-white/10 pr-2">
                    <div class="text-5xl font-bold text-white brand-font leading-none">{{ $totalSessions }}</div>
                    <div class="text-[8px] text-slate-300 uppercase tracking-widest font-bold mt-1">SESI LATIHAN</div>
                </div>
                <div class="flex-1 text-center pl-2">
                    <div class="flex items-end justify-center gap-1 leading-none">
                        <span class="text-5xl font-bold text-[#FBCC16] brand-font">{{ round($progressPercent) }}</span>
                        <span class="text-2xl font-bold text-[#FBCC16] brand-font mb-1">%</span>
                    </div>
                    <div class="text-[8px] text-slate-300 uppercase tracking-widest font-bold mt-1">KONSISTENSI</div>
                </div>
            </div>

            <div class="px-6 mb-4">
                <div class="bg-[#0b1120]/50 rounded-xl p-3 text-center border border-white/5">
                    <p class="text-sm font-bold text-white leading-tight brand-font tracking-wide">
                        "{{ $feedback }}"
                    </p>
                </div>
            </div>

            <div
                class="mt-auto bg-white rounded-t-[2rem] pt-5 pb-6 px-8 flex items-center gap-4 shadow-2xl relative z-20">
                <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="text-blue-600 w-5 h-5"></i>
                </div>
                <div class="flex flex-col">
                    <p class="text-[8px] text-slate-400 uppercase font-bold tracking-widest">MEMBER</p>
                    <h2 class="text-slate-900 font-bold text-2xl leading-none brand-font truncate max-w-[200px]">
                        {{ strtoupper($member->nama_lengkap) }}
                    </h2>
                </div>
            </div>

        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>