@extends('layouts.app')
@section('title', 'S-qutilar natijasi')

@section('content')

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-medium">S-qutilar natijasi</h1>
        <a href="{{ route('sbox.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← Orqaga
        </a>
    </div>

    {{-- Asosiy ko'rsatkichlar --}}
    <div class="grid grid-cols-4 gap-3 mb-6">

        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <div class="text-xl font-medium
            {{ $evaluation['is_bijection'] ? 'text-green-700' : 'text-red-600' }}">
                {{ $evaluation['is_bijection'] ? 'HA' : "YO'Q" }}
            </div>
            <div class="text-xs text-gray-400 mt-1">Bijeksiya</div>
        </div>

        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <div class="text-xl font-medium">
                {{ $evaluation['differential_uniformity'] }}
            </div>
            <div class="text-xs text-gray-400 mt-1">Diff. uniformity</div>
        </div>

        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <div class="text-xl font-medium">
                {{ $evaluation['nonlinearity'] }}
            </div>
            <div class="text-xs text-gray-400 mt-1">Nonlinearity</div>
        </div>

        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <div class="text-xl font-medium text-sm">
                {{ $evaluation['rating'] }}
            </div>
            <div class="text-xs text-gray-400 mt-1">Umumiy baho</div>
        </div>

    </div>

    {{-- S-qutilar jadvali --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <h2 class="text-sm font-medium mb-4">S-qutilar (hex ko'rinishda)</h2>

        <div class="overflow-x-auto">
            <table class="text-xs font-mono w-full">
                <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left py-2 px-3 text-gray-400 font-normal w-12">x</th>
                    @for($i = 0; $i < 16; $i++)
                        <th class="py-2 px-2 text-gray-400 font-normal text-center">
                            {{ dechex($i) }}
                        </th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                @php
                    $rows = array_chunk($sbox, 16, true);
                @endphp
                @foreach($rows as $rowIndex => $row)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 px-3 text-gray-400">
                            {{ dechex($rowIndex) }}x
                        </td>
                        @foreach($row as $index => $val)
                            <td class="py-2 px-2 text-center
                            {{ $val === 0 ? 'text-gray-300' : 'text-gray-700' }}">
                                {{ strtoupper(str_pad(dechex($val), 2, '0', STR_PAD_LEFT)) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tahlil --}}
    <div class="grid grid-cols-2 gap-6 mb-6">

        {{-- Sifat baholash --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="text-sm font-medium mb-3">Kriptografik tahlil</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-1.5 border-b border-gray-50">
                    <span class="text-gray-500">Bijeksiya (bir-bir moslik)</span>
                    <span class="{{ $evaluation['is_bijection'] ? 'text-green-600' : 'text-red-500' }}">
                    {{ $evaluation['is_bijection'] ? 'Ha ✓' : "Yo'q ✗" }}
                </span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-50">
                    <span class="text-gray-500">Differensial uniformity</span>
                    <span class="font-medium">
                    {{ $evaluation['differential_uniformity'] }}
                        @if($evaluation['differential_uniformity'] <= 2)
                            <span class="text-green-600 text-xs">(optimal)</span>
                        @elseif($evaluation['differential_uniformity'] <= 4)
                            <span class="text-amber-600 text-xs">(yaxshi)</span>
                        @else
                            <span class="text-red-500 text-xs">(yomon)</span>
                        @endif
                </span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-50">
                    <span class="text-gray-500">Nonlinearity</span>
                    <span class="font-medium">{{ $evaluation['nonlinearity'] }}</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-gray-500">Maksimal chiziqli og'ish</span>
                    <span class="font-medium">{{ $evaluation['max_linear_bias'] }}</span>
                </div>
            </div>
        </div>

        {{-- Taqqoslash --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="text-sm font-medium mb-3">AES bilan taqqoslash</h2>
            <div class="space-y-3">

                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Differensial uniformity</span>
                        <span>Sizniki: {{ $evaluation['differential_uniformity'] }} | AES: 4</span>
                    </div>
                    <div class="bg-gray-100 rounded-full h-2">
                        @php
                            $diffPct = min(100, (4 / max(1, $evaluation['differential_uniformity'])) * 100);
                        @endphp
                        <div class="h-2 rounded-full {{ $diffPct >= 80 ? 'bg-green-500' : ($diffPct >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                             style="width: {{ $diffPct }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Nonlinearity</span>
                        <span>Sizniki: {{ $evaluation['nonlinearity'] }} | Max: {{ pow(2, $n-1) - pow(2, $n/2-1) }}</span>
                    </div>
                    @php
                        $maxNl  = pow(2, $n - 1) - pow(2, $n / 2 - 1);
                        $nlPct  = $maxNl > 0 ? min(100, ($evaluation['nonlinearity'] / $maxNl) * 100) : 0;
                    @endphp
                    <div class="bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $nlPct >= 80 ? 'bg-green-500' : ($nlPct >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                             style="width: {{ $nlPct }}%"></div>
                    </div>
                </div>

            </div>

            <div class="mt-4 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500">Umumiy baho:</p>
                <p class="text-sm font-medium mt-1">{{ $evaluation['rating'] }}</p>
            </div>
        </div>

    </div>

    {{-- S-qutilar massivi (nusxa olish uchun) --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-medium">PHP massivi sifatida</h2>
            <button onclick="copyCode()"
                    class="text-xs text-blue-600 hover:underline">
                Nusxa olish
            </button>
        </div>
        <pre id="code-block"
             class="bg-gray-50 rounded-lg p-4 text-xs font-mono text-gray-700 overflow-x-auto">$sbox = [{{ implode(', ', $sbox) }}];</pre>
    </div>

    <script>
        function copyCode() {
            const code = document.getElementById('code-block').textContent;
            navigator.clipboard.writeText(code).then(() => {
                alert('Nusxa olindi!');
            });
        }
    </script>

@endsection
