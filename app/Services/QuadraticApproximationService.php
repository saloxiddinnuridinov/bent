<?php

namespace App\Services;

class QuadraticApproximationService
{
    /**
     * Tezlashtirilgan kvadratik yaqinlashuv
     * Avvalgi O(2^(n^2)) o'rniga O(n^2 * 2^n) algoritm
     */
    public function approximate(array $truthTable, int $n): array
    {
        $size = count($truthTable);

        // 1. Eng yaxshi affin (chiziqli) funksiyani topish
        $bestAffine   = $this->bestAffineApproximation($truthTable, $n);

        // 2. Kvadratik tuzatishlarni qo'shish
        $bestDistance = $this->hammingDistance($truthTable, $bestAffine['function']);
        $bestFunction = $bestAffine['function'];

        // Har bir kvadratik a_ij * x_i * x_j hadni sinab ko'rish
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                // Joriy eng yaxshi funksiyaga kvadratik had qo'shish
                $candidate = $this->addQuadraticTerm($bestFunction, $i, $j, $n, $size);
                $distance  = $this->hammingDistance($truthTable, $candidate);

                if ($distance < $bestDistance) {
                    $bestDistance = $distance;
                    $bestFunction = $candidate;
                }
            }
        }

        return [
            'quadratic'         => $bestFunction,
            'hamming_distance'  => $bestDistance,
            'relative_distance' => round($bestDistance / $size * 100, 2),
            'approximation_quality' => $this->quality($bestDistance, $size),
        ];
    }

    /**
     * Eng yaxshi affin funksiyani Walsh-Hadamard orqali topish
     * Bu O(n * 2^n) vaqt sarflaydi
     */
    private function bestAffineApproximation(array $truthTable, int $n): array
    {
        $size   = count($truthTable);
        $signal = array_map(fn($x) => $x === 0 ? 1 : -1, $truthTable);

        // WHT hisoblash
        $wht  = $signal;
        $step = 1;
        while ($step < $size) {
            for ($i = 0; $i < $size; $i += $step * 2) {
                for ($j = $i; $j < $i + $step; $j++) {
                    $u = $wht[$j];
                    $v = $wht[$j + $step];
                    $wht[$j]        = $u + $v;
                    $wht[$j + $step] = $u - $v;
                }
            }
            $step *= 2;
        }

        // Eng katta |W(a)| ni topish
        $maxVal = 0;
        $bestA  = 0;
        $bestC  = 0;

        foreach ($wht as $a => $val) {
            if (abs($val) > $maxVal) {
                $maxVal = abs($val);
                $bestA  = $a;
                $bestC  = $val < 0 ? 1 : 0;
            }
        }

        // Eng yaxshi affin funksiyani qurish: f(x) = <a,x> + c
        $affine = [];
        for ($x = 0; $x < $size; $x++) {
            $dot = 0;
            for ($i = 0; $i < $n; $i++) {
                $dot ^= (($x >> $i) & 1) & (($bestA >> $i) & 1);
            }
            $affine[] = $dot ^ $bestC;
        }

        return [
            'function' => $affine,
            'a'        => $bestA,
            'c'        => $bestC,
        ];
    }

    /**
     * Funksiyaga x_i * x_j kvadratik hadini qo'shish
     */
    private function addQuadraticTerm(
        array $f, int $i, int $j, int $n, int $size
    ): array {
        $result = $f;
        for ($x = 0; $x < $size; $x++) {
            $xi = ($x >> ($n - 1 - $i)) & 1;
            $xj = ($x >> ($n - 1 - $j)) & 1;
            $result[$x] ^= ($xi & $xj);
        }
        return $result;
    }

    /**
     * Hamming masofasi
     */
    public function hammingDistance(array $f, array $g): int
    {
        $distance = 0;
        $len      = count($f);
        for ($i = 0; $i < $len; $i++) {
            if ($f[$i] !== $g[$i]) {
                $distance++;
            }
        }
        return $distance;
    }

    /**
     * Yaqinlashuv sifatini baholash
     */
    private function quality(int $distance, int $size): string
    {
        $ratio = $distance / $size;

        if ($ratio === 0.0)  return 'Mukammal (chiziqli funksiya)';
        if ($ratio <= 0.1)   return 'Juda yaxshi';
        if ($ratio <= 0.2)   return 'Yaxshi';
        if ($ratio <= 0.35)  return 'O\'rtacha';
        return                      'Yomon';
    }
}
