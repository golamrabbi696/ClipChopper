<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentDeliverable;
use App\Models\User;
use Illuminate\Http\Request;

class DeliverableController extends Controller
{
    public function clients()
    {
        $clients = User::where('role', '!=', 'admin')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json($clients);
    }

    public function index()
    {
        $deliverables = ContentDeliverable::with('user')
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json($deliverables);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'  => 'required|exists:users,id',
            'month'    => 'required|string|max:10', // e.g. "2026-07"
            'type'     => 'required|in:video,post,quote',
            'title'    => 'required|string|max:255',
            'file_url' => 'required|url|max:2000',
            'notes'    => 'nullable|string|max:2000',
        ]);

        $deliverable = ContentDeliverable::create([
            'user_id'  => $validated['user_id'],
            'month'    => $validated['month'],
            'type'     => $validated['type'],
            'title'    => $validated['title'],
            'file_url' => $validated['file_url'],
            'notes'    => $validated['notes'] ?? null,
            'status'   => 'pending', // default
        ]);

        return response()->json([
            'message' => 'Deliverable created successfully.',
            'deliverable' => $deliverable->load('user')
        ], 201);
    }

    public function destroy($id)
    {
        $deliverable = ContentDeliverable::find($id);
        if (!$deliverable) {
            return response()->json(['message' => 'Deliverable not found.'], 404);
        }

        $deliverable->delete();

        return response()->json(['message' => 'Deliverable deleted successfully.']);
    }
}
