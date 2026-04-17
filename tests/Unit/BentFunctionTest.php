<?php
// tests/Unit/BentFunctionTest.php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\WalshHadamardService;
use App\Services\BentFunctionService;

class BentFunctionTest extends TestCase
{
    private BentFunctionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $wht           = new WalshHadamardService();
        $this->service = new BentFunctionService($wht);
    }

    /** @test */
    public function mashhur_bent_funksiya_n4(): void
    {
        // Maclaurin bent funksiyasi (n=4)
        $f = [0,1,1,0, 1,0,0,1, 1,0,0,1, 0,1,1,0];

        $this->assertTrue($this->service->isBent($f, 4));
        $this->assertEquals(6, $this->nonlinearity($f, 4));
    }

    /** @test */
    public function chiziqli_funksiya_bent_emas(): void
    {
        // f(x) = x1 — oddiy chiziqli funksiya
        $f = [0,0,0,0, 1,1,1,1, 0,0,0,0, 1,1,1,1];

        $this->assertFalse($this->service->isBent($f, 4));
    }

    /** @test */
    public function nonlinearity_hisoblash(): void
    {
        $f  = [0,1,1,0, 1,0,0,1, 1,0,0,1, 0,1,1,0];
        $nl = $this->nonlinearity($f, 4);

        // n=4 uchun max nonlinearity = 6
        $this->assertEquals(6, $nl);
    }

    private function nonlinearity(array $f, int $n): int
    {
        $wht  = new WalshHadamardService();
        $sig  = $wht->toSignal($f);
        $vals = $wht->transform($sig);
        return $this->service->nonlinearity($vals, $n);
    }
}
