<?php
// app/Services/WalshHadamardService.php

namespace App\Services;

class WalshHadamardService
{
    /**
     * Haqiqiy qiymat jadvalini +1/-1 signalga o'tkazish
     * f(x) = 0 => +1
     * f(x) = 1 => -1
     */
    public function toSignal(array $truthTable): array
    {
        return array_map(fn($x) => $x === 0 ? 1 : -1, $truthTable);
    }

    /**
     * Walsh-Hadamard Transformatsiyasi (tezlashtirilgan algoritm)
     * Vaqt murakkabligi: O(n * 2^n)
     */
    public function transform(array $signal): array
    {
        $n      = count($signal);
        $result = $signal;
        $step   = 1;

        while ($step < $n) {
            for ($i = 0; $i < $n; $i += $step * 2) {
                for ($j = $i; $j < $i + $step; $j++) {
                    $u = $result[$j];
                    $v = $result[$j + $step];
                    $result[$j]         = $u + $v;
                    $result[$j + $step] = $u - $v;
                }
            }
            $step *= 2;
        }

        return $result;
    }

    /**
     * Teskari WHT (normalizatsiya bilan)
     */
    public function inverseTransform(array $whtValues): array
    {
        $n      = count($whtValues);
        $result = $this->transform($whtValues);

        return array_map(fn($x) => $x / $n, $result);
    }
}
