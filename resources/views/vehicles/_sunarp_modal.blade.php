@include('partials.sunarp-modal', [
    'sunarpBtnId' => 'btnSunarp',
    'sunarpModalId' => 'modalSunarp',
    'sunarpSelectors' => [
        'plate' => 'input[name="plate"]',
        'brand' => 'select[name="brand_id"]',
        'model' => 'select[name="model_id"]',
        'year' => 'input[name="year"]',
        'color' => 'input[name="color"]',
        'vin' => 'input[name="vin"]',
        'engine' => 'input[name="engine_number"]',
    ],
])