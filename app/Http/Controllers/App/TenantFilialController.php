<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\TenantFilial;
use Illuminate\Http\Request;
use App\Models\Adress;
use App\Tenant\ManagerTenant;
use Illuminate\Support\Str;

class TenantFilialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = TenantFilial::with('addresses')->get();

        return view('app.filial.index', ['filiais' => $filiais]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('app.filial.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->all();

        if($request->hasFile('photo') && $request->file('photo')->isValid()){
            $name = Str::kebab($request->name);
            $extension = $request->photo->extension();
            $nameImage = "{$name}.$extension";
            $data['photo'] = $nameImage;

            $upload = $request->photo->storeAs('tenants/' . \app(ManagerTenant::class)->getTenant()->uuid . '/posts', $nameImage);

            if(!$upload)
                return redirect()->back()->with('errors', ['Falha de no Upload']);

        }


        $tenant = TenantFilial::create([
            'name' => $data['name'],
        ]);


        $data['tenant_id'] = $tenant->id;  // Adiciona o id da TenantFilial ao array de dados

        $address = Adress::create($data);

        return redirect()->route('filial.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
