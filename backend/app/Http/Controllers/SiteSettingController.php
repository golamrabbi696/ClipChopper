<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::singleton();
        $data = $this->buildResponse($settings);
        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'logo_type'            => 'required|in:text,image',
            'logo_text_mark'       => 'nullable|string|max:10',
            'logo_text_type'       => 'nullable|string|max:50',
            'logo_image'           => 'nullable|image|max:2048',
            'logo_image_dark'      => 'nullable|image|max:2048',
        ]);

        $settings = SiteSetting::singleton();

        // Handle light logo upload
        if ($request->hasFile('logo_image')) {
            if ($settings->logo_image_path) {
                Storage::disk('public')->delete($settings->logo_image_path);
            }
            $path = $request->file('logo_image')->store('logos', 'public');
            $settings->logo_image_path = $path;

            // Copy to frontend public so static <img> is always current
            $this->copyToFrontend($path, 'logo.png');
        }

        // Handle dark logo upload
        if ($request->hasFile('logo_image_dark')) {
            if ($settings->logo_image_path_dark) {
                Storage::disk('public')->delete($settings->logo_image_path_dark);
            }
            $pathDark = $request->file('logo_image_dark')->store('logos', 'public');
            $settings->logo_image_path_dark = $pathDark;

            // Copy to frontend public
            $this->copyToFrontend($pathDark, 'logo-dark.png');
        }

        $settings->logo_type       = $validated['logo_type'];
        $settings->logo_text_mark  = $validated['logo_text_mark'] ?? $settings->logo_text_mark;
        $settings->logo_text_type  = $validated['logo_text_type'] ?? $settings->logo_text_type;
        $settings->save();

        return response()->json([
            'message'  => 'Site branding settings updated successfully.',
            'settings' => $this->buildResponse($settings),
        ]);
    }

    private function buildResponse(SiteSetting $settings): array
    {
        $data = $settings->toArray();
        $data['logo_image_url']      = $settings->logo_image_path
            ? asset('storage/' . $settings->logo_image_path)
            : null;
        $data['logo_image_url_dark'] = $settings->logo_image_path_dark
            ? asset('storage/' . $settings->logo_image_path_dark)
            : null;
        return $data;
    }

    private function copyToFrontend(string $storagePath, string $filename): void
    {
        $frontendPath = base_path('../frontend/public/images/' . $filename);
        $dir = dirname($frontendPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        copy(Storage::disk('public')->path($storagePath), $frontendPath);
    }
}
