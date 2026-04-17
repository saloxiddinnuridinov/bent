@extends('layouts.app')
@section('title', 'S-qutilar generatori')

@section('content')

    <div class="mb-8">
        <h1 class="text-2xl font-medium mb-2">S-qutilar generatori</h1>
        <p class="text-gray-500 text-sm">
            Bent funksiyalar asosida kriptografik S-qutilar yarating
        </p>
    </div>

    {{-- Qisqacha tushuntirish --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6 text-sm text-blue-800">
        <p class="font-medium mb-1">S-qutilar nima?</p>
        <p class="text-blue-700">
            S-qutilar (Substitution boxes) blokli shifrlashdagi asosiy chiziqsizlik
            manbai. AES, DES kabi shifrlarda ishlatiladi. Bent funksiyalar asosida
            qurilgan S-qutilar eng yuqori kriptografik xavfsizlikni ta'minlaydi.
        </p>
    </div>

    {{-- Forma --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <form action="{{ route('sbox.generate') }}" method="POST" id="sbox-form">
            @csrf

            {{-- n tanlash --}}
            <div class="mb-5">
                <label class="text-sm text-gray-500 block mb-1.5">
                    O'zgaruvchilar soni (n)
                </label>
                <select name="n" id="n-select"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white w-48">
                    <option value="4" selected>n = 4 (4→4 bit S-qutilar)</option>
                    <option value="6">n = 6 (6→6 bit S-qutilar)</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">
                    n=4 uchun 4 ta bent funksiya kerak, n=6 uchun 6 ta
                </p>
            </div>

            {{-- Bent funksiyalar kiritish --}}
            <div class="mb-5">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm text-gray-500">
                        Bent funksiyalar
                        <span class="text-gray-400">(har biri alohida qatorda)</span>
                    </label>
                    <button type="button" id="load-examples"
                            class="text-xs text-blue-600 hover:underline">
                        Namuna yuklash
                    </button>
                </div>

                <div class="space-y-2" id="bent-inputs">
                    {{-- n=4 uchun 4 ta input --}}
                    @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400 w-16">f{{ $i + 1 }}(x):</span>
                            <input type="text"
                                   name="bent_functions[]"
                                   class="flex-1 border border-gray-200 rounded-lg px-3 py-2
                                  text-sm font-mono
                                  @error('bent_functions') border-red-300 @enderror"
                                   placeholder="0110100110010110"
                                   value="{{ old('bent_functions.'.$i) }}">
                        </div>
                    @endfor
                </div>

                @error('bent_functions')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Namuna bent funksiyalar (yashirin) --}}
            <div id="examples-n4" class="hidden">
                <p class="text-xs text-gray-500 mb-2">n=4 uchun tayyor bent funksiyalar:</p>
                <div class="space-y-1 font-mono text-xs text-gray-600">
                    <div>f1: 0110100110010110</div>
                    <div>f2: 0101101001011010</div>
                    <div>f3: 0011001111001100</div>
                    <div>f4: 0000111100001111</div>
                </div>
            </div>

            <button type="submit"
                    class="bg-blue-50 text-blue-700 border border-blue-200
                       px-5 py-2 rounded-lg text-sm hover:bg-blue-100 transition-colors">
                S-qutilar generatsiya qilish
            </button>
        </form>
    </div>

    {{-- Ma'lumotnoma --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="text-sm font-medium mb-3">S-qutilar sifat mezonlari</h2>
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="font-medium text-gray-700 mb-1">Differensial uniformity</div>
                <div class="text-xs text-gray-500">
                    Optimal: 2 (AES S-qutilaridek)<br>
                    Qabul qilinadigan: ≤ 4
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="font-medium text-gray-700 mb-1">Nonlinearity</div>
                <div class="text-xs text-gray-500">
                    n=4 uchun max: 6<br>
                    Yuqori bo'lsa yaxshi
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="font-medium text-gray-700 mb-1">Bijeksiya</div>
                <div class="text-xs text-gray-500">
                    Har bir chiqish qiymati<br>
                    bir marta bo'lishi shart
                </div>
            </div>
        </div>
    </div>

    <script>
        const nSelect    = document.getElementById('n-select');
        const bentInputs = document.getElementById('bent-inputs');
        const loadBtn    = document.getElementById('load-examples');

        // n o'zgarganda inputlar sonini yangilash
        nSelect.addEventListener('change', function () {
            const n       = parseInt(this.value);
            bentInputs.innerHTML = '';

            for (let i = 0; i < n; i++) {
                const placeholder = n === 4
                    ? '0110100110010110'
                    : '0'.repeat(64);

                bentInputs.innerHTML += `
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 w-16">f${i + 1}(x):</span>
                    <input type="text"
                           name="bent_functions[]"
                           class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono"
                           placeholder="${placeholder}">
                </div>`;
            }
        });

        // Namuna yuklash
        loadBtn.addEventListener('click', function () {
            const n       = parseInt(nSelect.value);
            const inputs  = bentInputs.querySelectorAll('input');

            const examples4 = [
                '0110100110010110',
                '0101101001011010',
                '0011001111001100',
                '0000111100001111',
            ];

            if (n === 4) {
                inputs.forEach((input, i) => {
                    input.value = examples4[i] || '';
                });
            }
        });
    </script>

@endsection
