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
        
        // If there's an image path, return the full asset URL
        $data = $settings->toArray();
        if ($settings->logo_image_path) {
            $data['logo_image_url'] = asset('storage/' . $settings->logo_image_path);
        } else {
            $data['logo_image_url'] = null;
        }

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'logo_type'       => 'required|in:text,image',
            'logo_text_mark'  => 'nullable|string|max:10',
            'logo_text_type'  => 'nullable|string|max:50',
            'logo_image'      => 'nullable|image|max:2048', // max 2MB
        ]);

        $settings = SiteSetting::singleton();

        // Handle image upload
        if ($request->hasFile('logo_image')) {
            // Delete old file if exists
            if ($settings->logo_image_path) {
                Storage::disk('public')->delete($settings->logo_image_path);
            }
            // Store new file
            $path = $request->file('logo_image')->store('logos', 'public');
            $settings->logo_image_path = $path;

            // Also copy to frontend public folder so static <img> tag always shows latest logo
            $frontendLogoPath = base_path('../frontend/public/images/logo.png');
            if (!is_dir(dirname($frontendLogoPath))) {
                mkdir(dirname($frontendLogoPath), 0755, true);
            }
            copy(Storage::disk('public')->path($path), $frontendLogoPath);
        }

        $settings->logo_type = $validated['logo_type'];
        $settings->logo_text_mark = $validated['logo_text_mark'] ?? $settings->logo_text_mark;
        $settings->logo_text_type = $validated['logo_text_type'] ?? $settings->logo_text_type;
        $settings->save();

        $data = $settings->toArray();
        if ($settings->logo_image_path) {
            $data['logo_image_url'] = asset('storage/' . $settings->logo_image_path);
        } else {
            $data['logo_image_url'] = null;
        }

        return response()->json([
            'message' => 'Site branding settings updated successfully.',
            'settings' => $data
        ]);
    }
}
