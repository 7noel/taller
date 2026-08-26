<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\CheckIn;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CheckInPdfTest extends TestCase
{
    use RefreshDatabase;

    private Establishment $establishment;
    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $brand = Brand::create(['name' => 'TOYOTA']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'COROLLA']);

        $this->establishment = Establishment::create([
            'name' => 'Taller Central',
            'address' => 'Av. Principal 123',
            'phone' => '123456789',
            'email' => 'contacto@taller.com',
            'code' => 'TC001',
        ]);

        $this->vehicle = Vehicle::factory()->create([
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'plate' => 'ABC123',
        ]);

        // El servicio asigna el número IV01 al crear check-ins.
        $documentType = DocumentType::create([
            'code' => 'IV',
            'name' => 'Inventario Vehicular',
            'is_electronic' => false,
            'is_active' => true,
        ]);
        DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $documentType->id,
            'prefix_serie' => 'IV01',
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);
    }

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role = Role::firstOrCreate(['name' => 'CheckInPdf Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    protected function makeCheckIn(User $user): CheckIn
    {
        return CheckIn::create([
            'vehicle_id' => $this->vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'service_type' => 'preventivo',
            'mileage' => 50000,
            'fuel_level' => 'medio',
            'property_card' => 'fisica',
            'keys_count' => 2,
            'has_remote_control' => true,
            'client_request' => 'Revisar frenos',
            'status' => 'draft',
        ]);
    }

    /**
     * Archivos PDF cacheados (excluye el .gitignore de la carpeta).
     */
    protected function cachedPdfs(): array
    {
        return collect(Storage::disk('local')->files('check-in-pdfs'))
            ->filter(fn (string $file) => str_ends_with($file, '.pdf'))
            ->values()
            ->all();
    }

    public function test_pdf_is_generated_and_cached_in_storage(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios']);
        $checkIn = $this->makeCheckIn($user);

        $response = $this->actingAs($user)->get(route('check-ins.pdf', $checkIn));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');

        $files = $this->cachedPdfs();

        $this->assertCount(1, $files);
        $this->assertStringContainsString('inventario-'.$checkIn->id.'-', $files[0]);
        $this->assertStringEndsWith('.pdf', $files[0]);
    }

    public function test_pdf_cache_is_reused_without_regenerating(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios']);
        $checkIn = $this->makeCheckIn($user);

        $this->actingAs($user)->get(route('check-ins.pdf', $checkIn))->assertOk();
        $filesAfterFirst = $this->cachedPdfs();
        $this->assertCount(1, $filesAfterFirst);

        // Segunda visita: mismo archivo (no se regenera).
        $this->actingAs($user)->get(route('check-ins.pdf', $checkIn))->assertOk();
        $this->assertSame($filesAfterFirst, $this->cachedPdfs());
    }

    public function test_pdf_cache_is_invalidated_when_check_in_is_updated(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'editar inventarios']);
        $checkIn = $this->makeCheckIn($user);

        $this->actingAs($user)->get(route('check-ins.pdf', $checkIn))->assertOk();
        $firstFile = $this->cachedPdfs()[0];

        $checkIn->update(['mileage' => 99999]);

        $this->actingAs($user)->get(route('check-ins.pdf', $checkIn))->assertOk();

        $files = $this->cachedPdfs();
        $this->assertCount(1, $files);
        $this->assertNotSame($firstFile, $files[0]);
    }

    public function test_pdf_clear_command_removes_cached_files(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios']);
        $checkIn = $this->makeCheckIn($user);

        $this->actingAs($user)->get(route('check-ins.pdf', $checkIn))->assertOk();
        $this->assertCount(1, $this->cachedPdfs());

        $this->artisan('pdf:clear')->assertSuccessful();

        $this->assertCount(0, $this->cachedPdfs());
    }
}
