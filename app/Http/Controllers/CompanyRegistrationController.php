<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class CompanyRegistrationController extends Controller
{
    public function create()
    {
        return view('app');
    }

    public function store(Request $request)
    {
        // Step 1: Validate registration data
        $validated = $request->validate([
            'companyName' => ['required', 'string', 'max:255'],
            'ownerName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'confirmPassword' => ['required', 'same:password'],
        ]);

        try {
            // Step 2: Create a new tenant
            $tenant = Tenant::create([
                'id' => (string) Str::uuid(),
                'company_name' => $validated['companyName'],
            ]);

            // Step 3: Create a domain for the tenant
            $tenant->domains()->create([
                'domain' => $tenant->id . '.localhost',
            ]);

            // Step 4: Create owner inside tenant database
            $tenant->run(function () use ($validated) {
                User::create([
                    'name' => $validated['ownerName'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ]);
            });

            // Step 5: Return success response
            return response()->json([
                'message' => 'Company registered successfully!',
                'tenant_id' => $tenant->id,
            ], 201);

        } catch (Throwable $e) {
            // Clean up tenant if registration fails
            if (isset($tenant)) {
                try {
                    $tenant->delete();
                } catch (Throwable $cleanupError) {
                    report($cleanupError);
                }
            }

            report($e);

            return response()->json([
                'message' => 'Company registration failed. Please try again.',
            ], 500);
        }
    }
}
