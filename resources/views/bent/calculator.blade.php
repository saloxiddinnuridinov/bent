<x-app-layout>
    <div class="max-w-3xl mx-auto py-8">

        <h1 class="text-2xl font-medium mb-6">
            Bent funksiya hisob-kitobi
        </h1>

        <form action="{{ route('bent.calculate') }}" method="POST"
              class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            @csrf

            {{-- O'zgaruvchilar soni --}}
            <div class="mb-4">
                <label class="text-sm text-gray-500 block mb-1">
                    O'zgaruvchilar soni (n) — juft bo'lishi shart
                </label>
                <select name="n" class="border rounded-lg px-3 py-2 text-sm w-32">
                    <option value="4">n = 4</option>
                    <option value="6">n = 6</option>
                    <option value="8">n = 8</option>
                </select>
            </div>

            {{-- Haqiqiy qiymat jadvali --}}
            <div class="mb-4">
                <label class="text-sm text-gray-500 block mb-1">
                    Haqiqiy qiymat jadvali (faqat 0 va 1)
                </label>
                <input type="text"
                       name="truth_table"
                       value="{{ old('truth_table', '0110100110010110') }}"
                       class="border rounded-lg px-3 py-2 text-sm w-full font-mono"
                       placeholder="0110100110010110">
                @error('truth_table')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="bg-blue-50 text-blue-700 border border-blue-200
                     px-5 py-2 rounded-lg text-sm hover:bg-blue-100">
                Hisoblash
            </button>
        </form>

        {{-- Natija --}}
        @isset($isBent)
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-base font-medium mb-4 pb-3 border-b border-gray-100">
                    Natija
                </h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bent holati:</span>
                        @if($isBent)
                            <span class="bg-green-50 text-green-700 px-3 py-0.5 rounded-md text-xs">
              HA — Bent funksiya
            </span>
                        @else
                            <span class="bg-red-50 text-red-700 px-3 py-0.5 rounded-md text-xs">
              YO'Q — Bent emas
            </span>
                        @endif
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Nonlinearity:</span>
                        <span class="font-medium">{{ $nonlinearity }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Maksimal mumkin nonlinearity:</span>
                        <span class="font-medium">{{ $maxNl }}</span>
                    </div>
                </div>

                {{-- WHT qiymatlari --}}
                <div class="mt-4">
                    <p class="text-xs text-gray-500 mb-2">Walsh-Hadamard qiymatlari:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($whtValues as $val)
                            <span class="px-2 py-1 rounded text-xs font-mono font-medium
              {{ $val > 0 ? 'bg-blue-50 text-blue-700' : 'bg-red-50 text-red-700' }}">
              {{ $val > 0 ? '+' : '' }}{{ $val }}
            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endisset

    </div>
</x-app-layout>
