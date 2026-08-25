<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UbigeoSeeder::class,
            RolePermissionSeeder::class,
            CompanySettingSeeder::class,
            DocumentTypeSeeder::class,
            EstablishmentSeeder::class,
            DocumentSeriesSeeder::class,
            UserSeeder::class,
            BrandModelSeeder::class,
            InsuranceCompanySeeder::class,
            PartySeeder::class,
            VehicleSeeder::class,
            VehicleRelationshipSeeder::class,
            ChecklistItemsSeeder::class,
            CheckInSeeder::class,
            CatalogSeeder::class,
            WarehouseSeeder::class,
            RepairServiceSeeder::class,
            PartSeeder::class,
            StockSeeder::class,
            EstimateSeeder::class,
        ]);
    }
}