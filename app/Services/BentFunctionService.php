<?php
// app/Services/BentFunctionService.php

namespace App\Services;

class BentFunctionService
{
    public function __construct(
        private WalshHadamardService $wht
    ) {}

    /**
     * Asosiy bent tekshiruvi
     * Bent shart: barcha WHT qiymatlari |W(f)| = 2^(n/2) bo'lishi kerak
     */
    public function isBent(array $truthTable, int $n): bool
    {
        // n juft bo'lishi shart
        if ($n % 2 !== 0) {
            return false;
        }

        $signal    = $this->wht->toSignal($truthTable);
        $whtValues = $this->wht->transform($signal);
        $expected  = pow(2, $n / 2);

        foreach ($whtValues as $val) {
            if (abs($val) !== $expected) {
                return false;
            }
        }

        return true;
    }

    /**
     * Nonlinearity hisoblash
     * N(f) = 2^(n-1) - (1/2) * max|W(f)(a)|
     */
    public function nonlinearity(array $whtValues, int $n): int
    {
        $maxWht = max(array_map('abs', $whtValues));

        return (int) (pow(2, $n - 1) - $maxWht / 2);
    }

    /**
     * Maksimal mumkin nonlinearity (bent funksiya uchun)
     * N_max = 2^(n-1) - 2^(n/2 - 1)
     */
    public function maxNonlinearity(int $n): int
    {
        return (int) (pow(2, $n - 1) - pow(2, $n / 2 - 1));
    }

    /**
     * Algebraik darajani hisoblash
     * ANF (Algebraic Normal Form) orqali
     */
    public function algebraicDegree(array $truthTable, int $n): int
    {
        $anf    = $this->computeANF($truthTable, $n);
        $degree = 0;

        foreach ($anf as $index => $coeff) {
            if ($coeff === 1) {
                // Indeksdagi 1-bitlar soni = monomial darajasi
                $degree = max($degree, $this->hammingWeight($index));
            }
        }

        return $degree;
    }

    /**
     * ANF (Mobius transformatsiyasi)
     */
    public function computeANF(array $truthTable, int $n): array
    {
        $anf  = $truthTable;
        $size = pow(2, $n);

        for ($i = 0; $i < $n; $i++) {
            $step = pow(2, $i);
            for ($j = 0; $j < $size; $j++) {
                if ($j & $step) {
                    $anf[$j] ^= $anf[$j ^ $step];
                }
            }
        }

        return $anf;
    }

    /**
     * Correlation immunity darajasi
     */
    public function correlationImmunity(array $whtValues, int $n): int
    {
        $size = pow(2, $n);
        $ci   = 0;

        for ($weight = 1; $weight <= $n; $weight++) {
            $isCI = true;
            for ($a = 1; $a < $size; $a++) {
                if ($this->hammingWeight($a) === $weight) {
                    if ($whtValues[$a] !== 0) {
                        $isCI = false;
                        break;
                    }
                }
            }
            if ($isCI) {
                $ci = $weight;
            } else {
                break;
            }
        }

        return $ci;
    }

    /**
     * Differensial uniformity (S-qutilar uchun)
     */
    public function differentialUniformity(array $sbox, int $n): int
    {
        $size    = pow(2, $n);
        $maxDiff = 0;

        for ($a = 1; $a < $size; $a++) {
            $diffTable = array_fill(0, $size, 0);
            for ($x = 0; $x < $size; $x++) {
                $output          = $sbox[$x] ^ $sbox[$x ^ $a];
                $diffTable[$output]++;
            }
            $maxDiff = max($maxDiff, max($diffTable));
        }

        return $maxDiff;
    }

    /**
     * Hamming og'irligi (1-bitlar soni)
     */
    private function hammingWeight(int $n): int
    {
        return substr_count(decbin($n), '1');
    }
}
