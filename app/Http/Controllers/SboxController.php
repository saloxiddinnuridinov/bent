<?php
// app/Http/Controllers/SboxController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SboxService;
use App\Services\BentFunctionService;
use App\Services\WalshHadamardService;

class SboxController extends Controller
{
    public function __construct(
        private SboxService         $sbox,
        private BentFunctionService $bent,
        private WalshHadamardService $wht
    ) {}

    public function index()
    {
        return view('sbox.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'bent_functions'   => 'required|array|min:4|max:8',
            'bent_functions.*' => 'required|string|regex:/^[01]+$/',
            'n'                => 'required|integer|in:4,6,8',
        ]);

        $n             = (int) $request->n;
        $bentFunctions = [];

        foreach ($request->bent_functions as $f) {
            $tt = array_map('intval', str_split($f));

            // Har bir funksiya bent ekanligini tekshirish
            if (!$this->bent->isBent($tt, $n)) {
                return back()->withErrors([
                    'bent_functions' => "Kiritilgan funksiyalardan biri bent emas: {$f}"
                ]);
            }
            $bentFunctions[] = $tt;
        }

        $sbox       = $this->sbox->generateFromBent($bentFunctions);
        $evaluation = $this->sbox->evaluate($sbox, $n);

        return view('sbox.result', [
            'sbox'       => $sbox,
            'evaluation' => $evaluation,
            'n'          => $n,
        ]);
    }
}
