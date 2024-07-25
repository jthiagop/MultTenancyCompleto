<?php

namespace App\Http\Controllers\App;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\TenantFilial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dateHelper = new DateHelper();

        // Recupere o usuário logado
        $user = auth()->user();

        // Filtrar os usuários pelo usuário logado
        $company = DB::table('users')
            ->join('company_user', 'users.id', '=', 'company_user.user_id')
            ->join('companies', 'company_user.company_id', '=', 'companies.id')
            ->where('users.id', $user->id) // Filtra pelo usuário logado
            ->select('users.*', 'company_user.company_id', 'companies.name as companies_name')
            ->get();


        return view('app.dashboard', ['company' => $company]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Obter o usuário pelo ID
        $user = User::findOrFail($id);

        // Carregar a empresa relacionada
        $company = $user->company;

        // Retornar a visão com os dados do usuário e da empresa
        return view('app.dashboard', compact('user', 'company'));
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
