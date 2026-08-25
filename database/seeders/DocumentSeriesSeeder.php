<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Services\DocumentSeriesService;
use Illuminate\Database\Seeder;

class DocumentSeriesSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(DocumentSeriesService::class);

        Establishment::query()
            ->orderBy('id')
            ->get()
            ->each(fn (Establishment $establishment) => $service->generateSeriesForEstablishment($establishment->id));
    }
}