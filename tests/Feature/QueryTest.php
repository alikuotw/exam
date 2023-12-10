<?php

namespace Tests\Feature;

use App\Http\Controllers\CurrencyController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QueryTest extends TestCase
{
    public function test_exchange(): void
    {
        $source = 'USD';
        $target = 'JPY';
        $amount = '$1,525';

        $response = $this->json('GET', '/api/currencies/query', [
            'source' => $source,
            'target' => $target,
            'amount' => $amount,
        ]);

        $responseData = $response->json();
        $result = '$170,496.53';

        $response->assertStatus(200);

        $this->assertEquals($result, $responseData['amount']);
    }

    public function test_amount_invalid(): void
    {
        $source = 'USD';
        $target = 'JPY';
        $amount = 'abcdefg';

        $response = $this->json('GET', '/api/currencies/query', [
            'source' => $source,
            'target' => $target,
            'amount' => $amount,
        ]);

        $response->assertStatus(400);
    }

    public function test_currencies_invalid(): void
    {
        $source = 'VND';
        $target = 'JPY';
        $amount = '1000';

        $response = $this->json('GET', '/api/currencies/query', [
            'source' => $source,
            'target' => $target,
            'amount' => $amount,
        ]);

        $response->assertStatus(400);
    }
}
