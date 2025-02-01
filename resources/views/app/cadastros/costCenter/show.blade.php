<x-tenant-app-layout>
    <div>
        @foreach ($transacoes as $t)
            <div> $t->descricao; </div>
        @endforeach
    </div>
thiago
</x-tenant-app-layout>
