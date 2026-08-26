<?php

namespace Tests\Feature;

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompanySettingLogoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create();
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role = Role::firstOrCreate(['name' => 'CompanySetting Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    public function test_logo_is_resized_and_compressed_on_upload(): void
    {
        $user = $this->createUserWithPermissions(['editar configuración']);

        // Imagen grande generada con GD (1200x800) para comprobar la reducción.
        $src = imagecreatetruecolor(1200, 800);
        $white = imagecolorallocate($src, 255, 255, 255);
        imagefill($src, 0, 0, $white);
        $tmp = tempnam(sys_get_temp_dir(), 'logo').'.jpg';
        imagejpeg($src, $tmp, 95);
        imagedestroy($src);

        $file = new UploadedFile($tmp, 'logo.jpg', 'image/jpeg', null, true);

        $this->actingAs($user)
            ->call('PUT', route('company-settings.update'), [
                'igv_rate' => 0.18,
                'default_number_source' => 'LOCAL',
                'facturador_provider' => 'local',
            ], [], ['logo' => $file])
            ->assertRedirect();

        $setting = CompanySetting::get();
        $this->assertNotNull($setting->logo_path);

        $storedPath = Storage::disk('public')->path($setting->logo_path);
        [$width, $height] = getimagesize($storedPath);

        $this->assertLessThanOrEqual(600, $width);
        $this->assertLessThanOrEqual(600, $height);

        @unlink($tmp);
    }
}
