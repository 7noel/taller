<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Ubigeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'igv_rate' => ['required', 'numeric', 'min:0', 'max:1'],
            'default_number_source' => ['required', 'in:LOCAL,API'],
            'facturador_provider' => ['required', 'in:local,nubefact,propio'],
            'facturador_api_url' => ['nullable', 'url', 'max:255'],
            'facturador_api_key' => ['nullable', 'string', 'max:255'],
            'facturador_secret' => ['nullable', 'string', 'max:255'],
            'whatsapp_api_url' => ['nullable', 'url', 'max:255'],
            'whatsapp_api_token' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,ico', 'max:1024'],
        ]);

        $setting = CompanySetting::get() ?? new CompanySetting();

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('company', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon_path) {
                Storage::disk('public')->delete($setting->favicon_path);
            }
            $validated['favicon_path'] = $request->file('favicon')->store('company', 'public');
        }

        $setting->fill($validated)->save();

        return redirect()->route('company-settings.edit')
            ->with('success', 'Configuración de empresa actualizada correctamente.');
    }
}