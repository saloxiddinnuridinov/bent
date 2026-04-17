@extends('layouts.app')
@section('title', 'Bent funksiya hisoblash')

@section('content')

    {{-- Sarlavha --}}
    <div class="mb-8">
        <h1 class="text-2xl font-medium mb-2">Bent funksiya tahlili</h1>
        <p class="text-gray-500 text-sm">
            Haqiqiy qiymat jadvalini kiriting — tizim bent xossalarini avtomatik aniqlaydi
        </p>
    </div>

    {{-- Hisoblash formasi --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <form action="{{ route('bent.calculate') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-6 mb-5">

                {{-- n tanlash --}}
                <div>
                    <label class="text-sm text-gray-500 block mb-1.5">
                        O'zgaruvchilar soni (n)
                    </label>
                    <select name="n"
                            id="n-select"
                            class="w-full border border-gray-200 rounded-lg
                               px-3 py-2 text-sm bg-white">
                        <option value="4" {{ old('n', 4) == 4 ? 'selected' : '' }}>n = 4  (16 ta qiymat)</option>
                        <option value="6" {{ old('n') == 6 ? 'selected' : '' }}>n = 6  (64 ta qiymat)</option>
                        <option value="8" {{ old('n') == 8 ? 'selected' : '' }}>n = 8  (256 ta qiymat)</option>
                    </select>
                </div>

                {{-- Namunaviy funksiyalar --}}
                <div>
                    <label class="text-sm text-gray-500 block mb-1.5">
                        Namunalar
                    </label>
                    <select id="examples"
                            class="w-full border border-gray-200 rounded-lg
                               px-3 py-2 text-sm bg-white">
                        <option value="">— Namuna tanlang —</option>
                        <option value="0110100110010110">Bent n=4 (Maclaurin)</option>
                        <option value="0000000001010101">Chiziqli n=4 (bent emas)</option>
                        <option value="0110100110010110">Kvadratik bent n=4</option>
                    </select>
                </div>
            </div>

            {{-- Haqiqiy qiymat jadvali --}}
            <div class="mb-5">
                <label class="text-sm text-gray-500 block mb-1.5">
                    Haqiqiy qiymat jadvali
                    <span class="text-gray-400">(faqat 0 va 1)</span>
                </label>
                <input type="text"
                       name="truth_table"
                       id="truth-table"
                       value="{{ old('truth_table', '0110100110010110') }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2
                          text-sm font-mono
                          @error('truth_table') border-red-300 @enderror"
                       placeholder="0110100110010110">

                @error('truth_table')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <p class="text-gray-400 text-xs mt-1" id="length-hint">
                    Joriy uzunlik: <span id="tt-length">16</span> ta
                    (n=4 uchun 16 ta kerak)
                </p>
            </div>

            {{-- Tugmalar --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-50 text-blue-700 border border-blue-200
                           px-5 py-2 rounded-lg text-sm
                           hover:bg-blue-100 transition-colors">
                    Tahlil qilish
                </button>
                <button type="reset"
                        class="text-gray-500 border border-gray-200
                           px-5 py-2 rounded-lg text-sm
                           hover:bg-gray-50 transition-colors">
                    Tozalash
                </button>
            </div>
        </form>
    </div>

    {{-- Saqlangan funksiyalar --}}
    @if($saved->count() > 0)
        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="text-base font-medium mb-4">So'nggi saqlangan funksiyalar</h2>
            <div class="space-y-2">
                @foreach($saved as $f)
                    <div class="flex items-center justify-between py-2
                    border-b border-gray-50 last:border-0">
                        <div>
                            <span class="text-sm font-medium">{{ $f->name }}</span>
                            <span class="text-xs text-gray-400 ml-2 font-mono">
                    {{ $f->short_truth_table }}
                </span>
                        </div>
                        <div class="flex items-center gap-3">
                <span class="text-xs px-2 py-0.5 rounded
                    {{ $f->is_bent
                        ? 'bg-green-50 text-green-700'
                        : 'bg-red-50 text-red-700' }}">
                    {{ $f->is_bent ? 'Bent' : 'Bent emas' }}
                </span>
                            <a href="{{ route('bent.show', $f) }}"
                               class="text-xs text-blue-600 hover:underline">
                                Ko'rish
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- JavaScript --}}
    <script>
        // Uzunlikni real vaqtda ko'rsatish
        const ttInput    = document.getElementById('truth-table');
        const ttLength   = document.getElementById('tt-length');
        const nSelect    = document.getElementById('n-select');
        const examples   = document.getElementById('examples');

        function updateLength() {
            const len      = ttInput.value.length;
            const expected = Math.pow(2, parseInt(nSelect.value));
            ttLength.textContent = len;
            ttLength.className   = len === expected ? 'text-green-600' : 'text-red-500';
        }

        ttInput.addEventListener('input', updateLength);
        nSelect.addEventListener('change', updateLength);

        // Namunani tanlash
        examples.addEventListener('change', function () {
            if (this.value) {
                ttInput.value = this.value;
                updateLength();
            }
        });

        updateLength();
    </script>

@endsection
