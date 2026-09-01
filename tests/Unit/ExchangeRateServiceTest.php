<?php

namespace Tests\Unit;

use App\Jobs\FetchExchangeRateJob;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): ExchangeRateService
    {
        return app(ExchangeRateService::class);
    }

    public function test_returns_existing_rate_without_calling_api(): void
    {
        ExchangeRate::create([
            'date' => '2026-09-01',
            'currency' => 'USD',
            'buy_rate' => 3.70,
            'sell_rate' => 3.75,
            'source' => 'manual',
        ]);

        Http::fake();

        $rate = $this->service()->ensureRateForDate('2026-09-01');

        $this->assertNotNull($rate);
        $this->assertEquals(3.75, $rate->sell_rate);
        Http::assertNothingSent();
    }

    public function test_fetches_from_api_and_persists_when_missing(): void
    {
        Http::fake([
            'api.apis.net.pe/*' => Http::response([
                'fecha' => '2026-09-01',
                'compra' => 3.72,
                'venta' => 3.78,
            ]),
        ]);

        $rate = $this->service()->ensureRateForDate('2026-09-01');

        $this->assertNotNull($rate);
        $this->assertEquals(3.78, $rate->sell_rate);
        $this->assertEquals(3.72, $rate->buy_rate);
        $this->assertEquals('SUNAT', $rate->source);

        $this->assertDatabaseHas('exchange_rates', [
            'date' => '2026-09-01',
            'currency' => 'USD',
            'sell_rate' => 3.78,
            'source' => 'SUNAT',
        ]);
    }

    public function test_falls_back_to_latest_registered_when_api_fails(): void
    {
        ExchangeRate::create([
            'date' => '2026-08-31',
            'currency' => 'USD',
            'buy_rate' => 3.68,
            'sell_rate' => 3.73,
            'source' => 'manual',
        ]);

        Http::fake(['api.apis.net.pe/*' => Http::response([], 500)]);

        $rate = $this->service()->ensureRateForDate('2026-09-01');

        $this->assertNotNull($rate);
        $this->assertEquals('2026-08-31', $rate->date->toDateString());
        $this->assertEquals(3.73, $rate->sell_rate);
    }

    public function test_pen_returns_null(): void
    {
        Http::fake();

        $this->assertNull($this->service()->ensureRateForDate('2026-09-01', 'PEN'));
    }

    public function test_job_persists_rate_for_given_date(): void
    {
        Http::fake([
            'api.apis.net.pe/*' => Http::response([
                'fecha' => '2026-09-02',
                'compra' => 3.71,
                'venta' => 3.77,
            ]),
        ]);

        (new FetchExchangeRateJob('2026-09-02'))->handle($this->service());

        $this->assertDatabaseHas('exchange_rates', [
            'date' => '2026-09-02',
            'currency' => 'USD',
            'sell_rate' => 3.77,
            'source' => 'SUNAT',
        ]);
    }
}
