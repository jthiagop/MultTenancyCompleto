<?php

namespace App\Http\Controllers;

use App\Jobs\SeedTenantJob;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;


class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants['tenants'] = Tenant::with('domains')->get();

        return view('tenant.index', $tenants);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('tenant.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenants,email',
            'domain_name' => 'required|string|max:255|unique:domains,domain',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

        ]);

        // Aqui você define o suffix com o nome de domínio
        config(['tenancy.database.suffix' => $validateData['domain_name']]);

        $tenant = Tenant::create($validateData);

        // Agora você pode usar o suffix configurado para criar o domínio
        $tenant->domains()->create([
            'domain' => $validateData['domain_name'].'.'.config('app.domain')
        ]);

            // Dispara o job para seed do tenant
            SeedTenantJob::dispatch($tenant);

        return redirect()->route('tenants.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        //
    }

    public function dashboard()
    {

    }
}
