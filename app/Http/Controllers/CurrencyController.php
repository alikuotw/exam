<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function query(Request $request): JsonResponse
    {
        /**
         * 假設此為資料庫取得的資料
         *
         */
        $jsonData = '{"currencies":{"TWD":{"TWD":1,"JPY":3.669,"USD":0.03281},"JPY":{"TWD":0.26956,"JPY":1,"USD":0.00885},"USD":{"TWD":30.444,"JPY":111.801,"USD":1}}}';
        $data = json_decode($jsonData);

        $source = $request->get('source'); // 來源
        $target = $request->get('target'); // 目標
        $amount = $request->get('amount'); // 金額

        $currencyAmount = preg_replace('/[$,]/', '', $amount);
        $value = 0;

        try {
            $value = floatval($currencyAmount);

            if ($value === 0.0) throw new \Exception('參數錯誤');

            $sourceExchangeRate = $data->currencies->$source ?? null;
            $targetExchangeRate = $data->currencies->$target ?? null;

            if (!$sourceExchangeRate || !$targetExchangeRate) throw new \Exception('幣別不存在');
        } catch (\Exception $e) {
            return Response::json([
                'msg' => 'failed',
                'description' => $e->getMessage(),
            ], 400);
        }

        $exchangeRate = $data->currencies->$source;
        $result = bcmul($value, $exchangeRate->$target, 10);
        $formattedResult = number_format($result, 2);

        return Response::json([
            'msg' => 'success',
            'amount' => "\${$formattedResult}",
        ], 200);
    }
}
