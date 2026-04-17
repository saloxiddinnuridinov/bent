<?php
// app/Services/AnalysisService.php

namespace App\Services;

class AnalysisService
{
    public function __construct(
        private BentFunctionService $bent,
        private WalshHadamardService $wht,
        private QuadraticApproximationService $quadratic
    ) {}

    /**
     * Funksiyaning to'liq kriptografik tahlili
     */
    public function fullAnalysis(array $truthTable, int $n): array
    {
        $signal    = $this->wht->toSignal($truthTable);
        $whtValues = $this->wht->transform($signal);

        $isBent       = $this->bent->isBent($truthTable, $n);
        $nl           = $this->bent->nonlinearity($whtValues, $n);
        $maxNl        = $this->bent->maxNonlinearity($n);
        $algDegree    = $this->bent->algebraicDegree($truthTable, $n);
        $ci           = $this->bent->correlationImmunity($whtValues, $n);
        $anf          = $this->bent->computeANF($truthTable, $n);
        $quadApprox   = $this->quadratic->approximate($truthTable, $n);

        return [
            // Asosiy xossalar
            'is_bent'              => $isBent,
            'nonlinearity'         => $nl,
            'max_nonlinearity'     => $maxNl,
            'nl_efficiency'        => round($nl / $maxNl * 100, 1),

            // Algebraik xossalar
            'algebraic_degree'     => $algDegree,
            'correlation_immunity' => $ci,
            'anf'                  => $anf,

            // WHT ma'lumotlari
            'wht_values'           => $whtValues,
            'wht_max'              => max(array_map('abs', $whtValues)),
            'wht_spectrum'         => $this->spectrum($whtValues),

            // Kvadratik yaqinlashuv
            'quadratic_approx'     => $quadApprox,

            // Umumiy baho
            'overall_rating'       => $this->overallRating($isBent, $nl, $maxNl, $algDegree),
            'recommendations'      => $this->recommendations($isBent, $nl, $maxNl, $algDegree, $ci),
        ];
    }

    /**
     * WHT spektrini hisoblash
     */
    private function spectrum(array $whtValues): array
    {
        $counts = [];
        foreach ($whtValues as $val) {
            $key          = (string) $val;
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }
        ksort($counts);
        return $counts;
    }

    /**
     * Umumiy kriptografik baho
     */
    private function overallRating(bool $isBent, int $nl, int $maxNl, int $degree): string
    {
        if ($isBent && $degree >= 2) return 'A+ (Mukammal)';
        if ($nl === $maxNl)         return 'A  (Yuqori)';
        if ($nl >= $maxNl * 0.9)   return 'B  (Yaxshi)';
        if ($nl >= $maxNl * 0.75)  return 'C  (O\'rtacha)';
        return                             'D  (Past)';
    }

    /**
     * Tavsiyalar
     */
    private function recommendations(
        bool $isBent, int $nl, int $maxNl, int $degree, int $ci
    ): array {
        $recs = [];

        if (!$isBent) {
            $recs[] = 'Funksiya bent emas — S-qutilar uchun mos emas';
        }
        if ($nl < $maxNl) {
            $recs[] = "Nonlinearity oshirish mumkin: {$nl} / {$maxNl}";
        }
        if ($degree < 2) {
            $recs[] = 'Algebraik daraja juda past — chiziqli hujumlarga zaif';
        }
        if ($ci === 0) {
            $recs[] = 'Correlation immunity yo\'q — korrelyatsion hujumlarga zaif';
        }
        if (empty($recs)) {
            $recs[] = 'Barcha kriptografik xossalar optimal darajada';
        }

        return $recs;
    }
}
