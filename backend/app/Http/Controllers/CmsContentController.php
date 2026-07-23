<?php

namespace App\Http\Controllers;

use App\Models\CmsContent;
use Illuminate\Http\Request;

class CmsContentController extends Controller
{
    public function index()
    {
        return response()->json(CmsContent::getAllAsKeyValue());
    }

    public function update(Request $request)
    {
        $data = $request->json()->all();
        
        // If not parsed as JSON, fallback to standard request input
        if (empty($data)) {
            $data = $request->all();
        }

        foreach ($data as $key => $value) {
            // Only save if key is a string
            if (is_string($key)) {
                CmsContent::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        return response()->json([
            'message' => 'CMS content updated successfully.',
            'contents' => CmsContent::getAllAsKeyValue()
        ]);
    }
}
