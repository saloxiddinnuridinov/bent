<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BentFunctionService;
use App\Services\WalshHadamardService;
use App\Services\AnalysisService;
use App\Services\QuadraticApproximationService;
use App\Models\BentFunction;

class BentController extends Controller
{
    public function __construct(
        private BentFunctionService           $bent,
        private WalshHadamardService          $wht,
        private AnalysisService               $analysis,
        private QuadraticApproximationService $quadratic
    ) {}

    // Bosh sahifa
    public function index()
    {
        $saved = BentFunction::latest()->take(5)->get();
        return view('bent.index', compact('saved'));
    }

    // Hisoblash
    public function calculate(Request $request)
    {
        $request->validate([
            'truth_table' => [
                'required',
                'string',
                'regex:/^[01]+$/',
            ],
            'n' => 'required|integer|in:2,4,6,8',
        ], [
            'truth_table.required' => 'Haqiqiy qiymat jadvali kiritilishi shart',
            'truth_table.regex'    => 'Faqat 0 va 1 raqamlar kiritilishi mumkin',
            'n.in'                 => 'n faqat 2, 4, 6 yoki 8 bo\'lishi mumkin',
        ]);

        $n    = (int) $request->n;
        $bits = str_split($request->truth_table);

        // Uzunlik tekshiruvi
        $expected = pow(2, $n);
        if (count($bits) !== $expected) {
            return back()
                ->withInput()
                ->withErrors([
                    'truth_table' => "n={$n} uchun uzunlik {$expected} ta bo'lishi kerak, siz " . count($bits) . " ta kirdingiz"
                ]);
        }

        $truthTable = array_map('intval', $bits);

        // To'liq tahlil
        $result = $this->analysis->fullAnalysis($truthTable, $n);

        // Sessiyaga saqlash (keyingi sahifalarda ishlatish uchun)
        session(['last_result' => $result, 'last_tt' => $truthTable, 'last_n' => $n]);

        return view('bent.result', [
            'result'     => $result,
            'truthTable' => $truthTable,
            'n'          => $n,
            'input'      => $request->truth_table,
        ]);
    }

    // Natijani bazaga saqlash
    public function store(Request $request)
    {
        $request->validate([
            'truth_table' => 'required|string',
            'n'           => 'required|integer',
            'name'        => 'nullable|string|max:100',
        ]);

        $truthTable = array_map('intval', str_split($request->truth_table));
        $signal     = $this->wht->toSignal($truthTable);
        $whtValues  = $this->wht->transform($signal);

        BentFunction::create([
            'name'          => $request->name ?? 'Nomsiz funksiya',
            'truth_table'   => $request->truth_table,
            'n'             => $request->n,
            'is_bent'       => $this->bent->isBent($truthTable, $request->n),
            'nonlinearity'  => $this->bent->nonlinearity($whtValues, $request->n),
            'alg_degree'    => $this->bent->algebraicDegree($truthTable, $request->n),
        ]);

        return redirect()->route('bent.library')
            ->with('success', 'Funksiya muvaffaqiyatli saqlandi');
    }

    // Kutubxona
    public function library()
    {
        $functions = BentFunction::latest()->paginate(10);
        return view('bent.library', compact('functions'));
    }

    // Bitta funksiyani ko'rish
    public function show(BentFunction $bentFunction)
    {
        $truthTable = array_map('intval', str_split($bentFunction->truth_table));
        $result     = $this->analysis->fullAnalysis($truthTable, $bentFunction->n);

        return view('bent.show', [
            'function' => $bentFunction,
            'result'   => $result,
        ]);
    }

    // O'chirish
    public function destroy(BentFunction $bentFunction)
    {
        $bentFunction->delete();
        return redirect()->route('bent.library')
            ->with('success', 'Funksiya o\'chirildi');
    }
}
