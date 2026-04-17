<?php
// app/Services/SboxService.php

namespace App\Services;

class SboxService
{
    public function __construct(
        private BentFunctionService $bent,
        private WalshHadamardService $wht
    ) {}

    /**
     * Bent funksiyalar asosida 4-bitli S-qutilar generatsiya
     * Kirish: 4 bit, Chiqish: 4 bit
     */
    public function generateFromBent(array $bentFunctions): array
    {
        // 4 ta bent funksiyani birlashtirish
        $size = count($bentFunctions[0]);
        $sbox = [];

        for ($x = 0; $x < $size; $x++) {
            $output = 0;
            foreach ($bentFunctions as $i => $f) {
                $output |= ($f[$x] << $i);
            }
            $sbox[$x] = $output;
        }

        return $sbox;
    }

    /**
     * S-qutilar xossalarini baholash
     */
    public function evaluate(array $sbox, int $n): array
    {
        $size = count($sbox);

        // Bijection tekshiruvi
        $isBijection = count(array_unique($sbox)) === $size;

        // Differensial uniformity
        $diffUniformity = $this->bent->differentialUniformity($sbox, $n);

        // Chiziqlilik profili
        $linearProfile = $this->linearityProfile($sbox, $n);

        return [
            'is_bijection'         => $isBijection,
            'differential_uniformity' => $diffUniformity,
            'max_linear_bias'      => max($linearProfile),
            'nonlinearity'         => (int) (pow(2, $n - 1) - max($linearProfile) / 2),
            'rating'               => $this->rating($diffUniformity, max($linearProfile), $n),
        ];
    }

    /**
     * Chiziqlilik profili
     */
    private function linearityProfile(array $sbox, int $n): array
    {
        $size    = pow(2, $n);
        $profile = [];

        for ($a = 0; $a < $size; $a++) {
            for ($b = 1; $b < $size; $b++) {
                $count = 0;
                for ($x = 0; $x < $size; $x++) {
                    $lhs = $this->innerProduct($x, $a, $n);
                    $rhs = $this->innerProduct($sbox[$x], $b, $n);
                    if ($lhs === $rhs) {
                        $count++;
                    }
                }
                $profile[] = abs(2 * $count - $size);
            }
        }

        return $profile;
    }

    /**
     * Ichki ko'paytma (dot product) ikkilik ko'rinishda
     */
    private function innerProduct(int $x, int $a, int $n): int
    {
        $result = 0;
        for ($i = 0; $i < $n; $i++) {
            $result ^= (($x >> $i) & 1) & (($a >> $i) & 1);
        }
        return $result;
    }

    /**
     * S-qutilar sifatini baholash
     */
    private function rating(int $diffUniformity, int $maxBias, int $n): string
    {
        $optimalDiff = 2;
        $optimalBias = (int) pow(2, $n / 2);

        if ($diffUniformity === $optimalDiff && $maxBias === $optimalBias) {
            return 'Optimal (AES darajasi)';
        }
        if ($diffUniformity <= 4 && $maxBias <= $optimalBias * 2) {
            return 'Yaxshi';
        }
        return 'Qoniqarli';
    }
}
