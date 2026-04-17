<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BentFunction extends Model
{
    protected $fillable = [
        'name',
        'truth_table',
        'n',
        'is_bent',
        'nonlinearity',
        'alg_degree',
    ];

    protected $casts = [
        'is_bent' => 'boolean',
    ];

    // Haqiqiy qiymat jadvalini massiv sifatida olish
    public function getTruthTableArrayAttribute(): array
    {
        return array_map('intval', str_split($this->truth_table));
    }

    // Qisqa ko'rinish (birinchi 16 bit)
    public function getShortTruthTableAttribute(): string
    {
        return substr($this->truth_table, 0, 16) .
            (strlen($this->truth_table) > 16 ? '...' : '');
    }
}
