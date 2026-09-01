<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodBankSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['CASH', 'Efectivo'],
            ['CARD', 'Tarjeta de débito/crédito'],
            ['YAPE', 'Yape'],
            ['PLIN', 'Plin'],
            ['TRANSFER', 'Transferencia bancaria'],
            ['DEPOSIT', 'Depósito bancario'],
            ['CHECK', 'Cheque'],
        ] as [$code, $name]) {
            PaymentMethod::firstOrCreate(['code' => $code], ['name' => $name, 'is_active' => true]);
        }

        foreach ([
            'Banco de Crédito del Perú (BCP)',
            'BBVA',
            'Interbank',
            'Scotiabank',
            'Banco de la Nación',
            'BanBif',
            'MiBanco',
            'Caja Piura',
        ] as $bank) {
            Bank::firstOrCreate(['name' => $bank], ['is_active' => true]);
        }
    }
}
