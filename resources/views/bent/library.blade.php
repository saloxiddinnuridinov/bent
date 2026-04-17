@extends('layouts.app')
@section('title', 'Kutubxona')

@section('content')

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-medium">Funksiyalar kutubxonasi</h1>
        <a href="{{ route('bent.index') }}"
           class="bg-blue-50 text-blue-700 border border-blue-200
              px-4 py-2 rounded-lg text-sm hover:bg-blue-100">
            + Yangi hisoblash
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-medium text-gray-500">Nom</th>
                <th class="text-left px-5 py-3 font-medium text-gray-500">n</th>
                <th class="text-left px-5 py-3 font-medium text-gray-500">Holati</th>
                <th class="text-left px-5 py-3 font-medium text-gray-500">Nonlinearity</th>
                <th class="text-left px-5 py-3 font-medium text-gray-500">Daraja</th>
                <th class="text-right px-5 py-3 font-medium text-gray-500">Amallar</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($functions as $f)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <span class="font-medium">{{ $f->name }}</span>
                        <span class="text-xs text-gray-400 ml-2 font-mono">
                        {{ $f->short_truth_table }}
                    </span>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $f->n }}</td>
                    <td class="px-5 py-3">
                    <span class="text-xs px-2 py-0.5 rounded
                        {{ $f->is_bent
                            ? 'bg-green-50 text-green-700'
                            : 'bg-red-50 text-red-700' }}">
                        {{ $f->is_bent ? 'Bent' : 'Bent emas' }}
                    </span>
                    </td>
                    <td class="px-5 py-3">{{ $f->nonlinearity }}</td>
                    <td class="px-5 py-3">{{ $f->alg_degree }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('bent.show', $f) }}"
                               class="text-blue-600 hover:underline text-xs">
                                Ko'rish
                            </a>
                            <form action="{{ route('bent.destroy', $f) }}"
                                  method="POST"
                                  onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-500 hover:underline text-xs">
                                    O'chirish
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"
                        class="px-5 py-8 text-center text-gray-400 text-sm">
                        Hali hech narsa saqlanmagan
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($functions->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $functions->links() }}
            </div>
        @endif
    </div>

@endsection
