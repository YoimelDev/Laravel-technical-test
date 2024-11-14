<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CurrencyConverter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyConversionController extends Controller
{
    public function __construct(
        private readonly CurrencyConverter $currencyConverter
    ) {}

    public function convert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'string', 'size:3'],
            'to' => ['required', 'string', 'size:3'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $result = $this->currencyConverter->convert(
            $validated['from'],
            $validated['to'],
            $validated['amount'],
            $request->user()
        );

        return response()->json(['data' => $result]);
    }

    public function history(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        $history = $this->currencyConverter->getHistory(
            $validated['currency'] ?? null,
            $validated['from_date'] ?? null,
            $validated['to_date'] ?? null
        );

        return response()->json(['data' => $history]);
    }
}