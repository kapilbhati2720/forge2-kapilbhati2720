<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SlaPolicy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SlaPolicyController extends Controller
{
    public function index()
    {
        $policies = SlaPolicy::all();
        return response()->json(['data' => $policies]);
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->organization_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'priority' => [
                'required',
                'string',
                Rule::in(['low', 'medium', 'high', 'urgent']),
                Rule::unique('sla_policies')->where(fn ($query) => $query->where('organization_id', $orgId)),
            ],
            'response_minutes' => 'required|integer|min:1',
            'resolution_minutes' => 'required|integer|min:1',
            'is_default' => 'sometimes|boolean',
        ]);

        $validated['organization_id'] = $orgId;
        $isDefault = $validated['is_default'] ?? false;

        if ($isDefault) {
            SlaPolicy::where('organization_id', $orgId)->update(['is_default' => false]);
        }

        $policy = SlaPolicy::create($validated);

        return response()->json(['data' => $policy], 201);
    }

    public function show(SlaPolicy $slaPolicy)
    {
        return response()->json(['data' => $slaPolicy]);
    }

    public function update(Request $request, SlaPolicy $slaPolicy)
    {
        $orgId = auth()->user()->organization_id;

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'priority' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['low', 'medium', 'high', 'urgent']),
                Rule::unique('sla_policies')
                    ->where(fn ($query) => $query->where('organization_id', $orgId))
                    ->ignore($slaPolicy->id),
            ],
            'response_minutes' => 'sometimes|required|integer|min:1',
            'resolution_minutes' => 'sometimes|required|integer|min:1',
            'is_default' => 'sometimes|boolean',
        ]);

        if (isset($validated['is_default']) && $validated['is_default']) {
            SlaPolicy::where('organization_id', $orgId)->update(['is_default' => false]);
        }

        $slaPolicy->update($validated);

        return response()->json(['data' => $slaPolicy]);
    }

    public function destroy(SlaPolicy $slaPolicy)
    {
        $slaPolicy->delete();
        return response()->json(null, 204);
    }
}
