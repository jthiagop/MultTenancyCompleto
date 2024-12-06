<x-tenant-app-layout>

    <div class="container">
        <h1>Detalhes da Empresa</h1>
        <table class="table">
            <tr>
                <th>Nome:</th>
                <td>{{ $company->name }}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>{{ $company->email }}</td>
            </tr>
            <tr>
                <th>Endereço:</th>
                <td>{{ $company->address }}</td>
            </tr>
            <tr>
                <th>Data de Criação:</th>
                <td>{{ $company->created_at->format('d/m/Y') }}</td>
            </tr>
        </table>
        <a href="{{ route('company.index') }}" class="btn btn-primary">Voltar</a>
    </div>
</x-tenant-app-layout>
