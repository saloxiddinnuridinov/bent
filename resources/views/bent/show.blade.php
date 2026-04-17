{{-- resources/views/bent/show.blade.php --}}

@extends('layouts.app')
@section('title', $function->name)

@section('content')

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-medium">{{ $function->name }}</h1>
        <a href="{{ route('bent.library') }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← Kutubxonaga qaytish
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

        {{-- Funksiya ma'lumotlari --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h2 class="text-sm font-medium mb-3">Funksiya ma'lumotlari</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-1.5 border-b border-gray-50">
                    <span class="text-gray-500">O'zgaruvchilar soni</span>
                    <span class="font-medium">n = {{ $function->n }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-50">
                    <span class="text-gray-500">Jadval uzunligi</span>
                    <span class="font-medium">{{ strlen($function->truth_table) }} ta</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-gray-50">
                    <span class="text-gray-500">Saqlangan vaqt</span>
                    <span class="font-medium">{{ $function->created_at->format('d.m.Y H:i') }}</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Haqiqiy qiymat jadvali:</p>
                <p class="font-mono text-xs text-gray-600 break-all">
                    {{ $function->truth_table }}
                </p>
            </div>
        </div>

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
                Maksimal: ±{{ $result['wht_max'] }}
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

    {{-- O'chirish --}}
    <div class="flex justify-end">
        <form action="{{ route('bent.destroy', $function) }}"
              method="POST"
              onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="text-red-500 border border-red-200 bg-red-50
                       px-4 py-2 rounded-lg text-sm hover:bg-red-100">
                O'chirish
            </button>
        </form>
    </div>

@endsection
