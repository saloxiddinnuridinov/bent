@extends('layouts.app')
@section('title', 'Tahlil natijasi')

@section('content')

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-medium">Tahlil natijasi</h1>
        <a href="{{ route('bent.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← Orqaga
        </a>
    </div>

    {{-- Asosiy ko'rsatkichlar --}}
    <div class="grid grid-cols-4 gap-3 mb-6">
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <div class="text-xl font-medium
            {{ $result['is_bent'] ? 'text-green-700' : 'text-red-600' }}">
                {{ $result['is_bent'] ? 'HA' : "YO'Q" }}
            </div>
            <div class="text-xs text-gray-400 mt-1">Bent holati</div>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <div class="text-xl font-medium">{{ $result['nonlinearity'] }}</div>
            <div class="text-xs text-gray-400 mt-1">
                Nonlinearity / {{ $result['max_nonlinearity'] }}
            </div>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <div class="text-xl font-medium">{{ $result['algebraic_degree'] }}</div>
            <div class="text-xs text-gray-400 mt-1">Algebraik daraja</div>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <div class="text-xl font-medium">{{ $result['overall_rating'] }}</div>
            <div class="text-xs text-gray-400 mt-1">Umumiy baho</div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-6">

        {{-- WHT qiymatlari --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="text-sm font-medium mb-3">Walsh-Hadamard qiymatlari</h2>
            <div class="flex flex-wrap gap-1.5">
                @foreach($result['wht_values'] as $val)
                    <span class="px-2 py-1 rounded text-xs font-mono font-medium
                    {{ $val > 0
                        ? 'bg-blue-50 text-blue-700'
                        : 'bg-red-50 text-red-700' }}">
                    {{ $val > 0 ? '+' : '' }}{{ $val }}
                </span>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
                Maksimal qiymat: ±{{ $result['wht_max'] }}
                @if($result['is_bent'])
                    = 2^(n/2) ✓
                @endif
            </div>
        </div>

        {{-- Kriptografik xossalar --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="text-sm font-medium mb-3">Kriptografik xossalar</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-1.5 border-b border-gray-50">
                    <span class="text-gray-500">Nonlinearity samaradorligi</span>
                    <span class="font-medium">{{ $result['nl_efficiency'] }}%</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-50">
                    <span class="text-gray-500">Correlation immunity</span>
                    <span class="font-medium">{{ $result['correlation_immunity'] }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-50">
                    <span class="text-gray-500">WHT spektri</span>
                    <span class="font-medium font-mono text-xs">
                    @foreach($result['wht_spectrum'] as $val => $count)
                            {{ $val }}:{{ $count }}
                        @endforeach
                </span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-gray-500">Kvadratik yaqinlashuv sifati</span>
                    <span class="font-medium">
                    {{ $result['quadratic_approx']['approximation_quality'] }}
                </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tavsiyalar --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <h2 class="text-sm font-medium mb-3">Tavsiyalar</h2>
        <ul class="space-y-1.5">
            @foreach($result['recommendations'] as $rec)
                <li class="text-sm text-gray-600 flex items-start gap-2">
                    <span class="text-gray-400 mt-0.5">—</span>
                    {{ $rec }}
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Saqlash formasi --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="text-sm font-medium mb-3">Natijani saqlash</h2>
        <form action="{{ route('bent.store') }}" method="POST"
              class="flex gap-3 items-end">
            @csrf
            <input type="hidden" name="truth_table" value="{{ $input }}">
            <input type="hidden" name="n" value="{{ $n }}">
            <div class="flex-1">
                <label class="text-xs text-gray-500 block mb-1">Nom (ixtiyoriy)</label>
                <input type="text" name="name"
                       placeholder="Funksiya nomi..."
                       class="w-full border border-gray-200 rounded-lg
                          px-3 py-2 text-sm">
            </div>
            <button type="submit"
                    class="bg-green-50 text-green-700 border border-green-200
                       px-5 py-2 rounded-lg text-sm hover:bg-green-100">
                Saqlash
            </button>
        </form>
    </div>

@endsection
