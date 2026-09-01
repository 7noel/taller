<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Ubigeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanySettingController extends Controller
{
    public function edit(): View
    {
        Gate::authorize('ver configuración');

        $setting = CompanySetting::get() ?? new CompanySetting();
        $departamentos = Ubigeo::select('departamento')->distinct()->orderBy('departamento')->pluck('departamento');

        return view('company-settings.edit', compact('setting', 'departamentos'));
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('editar configuración');

        // Los checkboxes de recordatorios deben persistir false cuando se desmarcan.
        $request->merge([
            'reminder_enabled' => $request->boolean('reminder_enabled'),
            'reminder_technical_review_enabled' => $request->boolean('reminder_technical_review_enabled'),
            'reminder_maintenance_enabled' => $request->boolean('reminder_maintenance_enabled'),
            'reminder_part_order_enabled' => $request->boolean('reminder_part_order_enabled'),
            'reminder_estimate_enabled' => $request->boolean('reminder_estimate_enabled'),
        ]);

        $validated = $request->validate([
            'ruc' => ['nullable', 'string', 'max:11'],
            'razon_social' => ['nullable', 'string', 'max:255'],
            'nombre_comercial' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ubigeo_code' => ['nullable', 'string', 'size:6', 'exists:ubigeos,code'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'celular' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'detraccion_account' => ['nullable', 'string', 'max:20'],
            'detraccion_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'igv_rate' => ['required', 'numeric', 'min:0', 'max:1'],
            'default_number_source' => ['required', 'in:LOCAL,API'],
            'facturador_provider' => ['required', 'in:local,nubefact,propio'],
            'facturador_api_url' => ['nullable', 'url', 'max:255'],
            'facturador_api_key' => ['nullable', 'string', 'max:255'],
            'facturador_secret' => ['nullable', 'string', 'max:255'],
            'whatsapp_api_url' => ['nullable', 'url', 'max:255'],
            'whatsapp_api_token' => ['nullable', 'string', 'max:255'],
            'whatsapp_instance_name' => ['nullable', 'string', 'max:100'],
            'whatsapp_enabled' => ['sometimes', 'boolean'],
            'maintenance_interval_km' => ['nullable', 'integer', 'min:500', 'max:50000'],
            'maintenance_default_days' => ['nullable', 'integer', 'min:15', 'max:365'],
            'maintenance_history_visits' => ['nullable', 'integer', 'min:2', 'max:5'],
            'reminder_enabled' => ['required', 'boolean'],
            'reminder_hour' => ['nullable', 'date_format:H:i'],
            'reminder_technical_review_enabled' => ['required', 'boolean'],
            'reminder_technical_review_days' => ['nullable', 'integer', 'min:0', 'max:90'],
            'reminder_maintenance_enabled' => ['required', 'boolean'],
            'reminder_maintenance_days' => ['nullable', 'integer', 'min:0', 'max:90'],
            'reminder_part_order_enabled' => ['required', 'boolean'],
            'reminder_part_milestones' => ['nullable', 'string', 'max:100', 'regex:/^[0-9,\s]+$/'],
            'reminder_estimate_enabled' => ['required', 'boolean'],
            'reminder_estimate_every_days' => ['nullable', 'integer', 'min:1', 'max:60'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,ico', 'max:1024'],
        ]);

        $setting = CompanySetting::get() ?? new CompanySetting();

        if ($request->hasFile('logo')) {
            $this->optimizeImage($request->file('logo'), 600);

            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('company', 'public');
        }

        if ($request->hasFile('favicon')) {
            $this->optimizeImage($request->file('favicon'), 256);

            if ($setting->favicon_path) {
                Storage::disk('public')->delete($setting->favicon_path);
            }
            $validated['favicon_path'] = $request->file('favicon')->store('company', 'public');
        }

        $setting->fill($validated)->save();

        return redirect()->route('company-settings.edit')
            ->with('success', 'Configuración de empresa actualizada correctamente.');
    }

    /**
     * Redimensiona y comprime una imagen raster (jpg/png/webp) con GD.
     *
     * - Logo: máximo 600px en su lado mayor.
     * - Favicon: máximo 256px en su lado mayor.
     * - Los formatos no procesables con GD (svg, ico) o la ausencia de GD se
     *   ignoran sin romper la subida.
     */
    protected function optimizeImage(UploadedFile $file, int $maxDimension): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            return;
        }

        $path = $file->path();

        $info = @getimagesize($path);
        if ($info === false) {
            return;
        }

        [$width, $height, $type] = $info;

        $createFrom = match ($type) {
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG => 'imagecreatefrompng',
            IMAGETYPE_WEBP => 'imagecreatefromwebp',
            default => null,
        };

        if ($createFrom === null) {
            return;
        }

        $src = $createFrom($path);

        // Corrige la orientación EXIF de fotos (JPEG de celulares/cámaras).
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($path);
            $orientation = (int) ($exif['Orientation'] ?? 1);

            $rotated = match ($orientation) {
                3 => imagerotate($src, 180, 0),
                6 => imagerotate($src, -90, 0),
                8 => imagerotate($src, 90, 0),
                default => null,
            };

            if ($rotated !== null) {
                imagedestroy($src);
                $src = $rotated;
            }

            if ($orientation === 5 || $orientation === 7) {
                imageflip($src, IMG_FLIP_HORIZONTAL);
            }

            $width = imagesx($src);
            $height = imagesy($src);
        }

        $ratio = min(1.0, $maxDimension / max($width, $height));
        $newWidth = (int) max(1, round($width * $ratio));
        $newHeight = (int) max(1, round($height * $ratio));

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($dst, $path, 82),
            IMAGETYPE_PNG => imagepng($dst, $path, 6),
            IMAGETYPE_WEBP => imagewebp($dst, $path, 82),
            default => null,
        };

        imagedestroy($src);
        imagedestroy($dst);
    }
}