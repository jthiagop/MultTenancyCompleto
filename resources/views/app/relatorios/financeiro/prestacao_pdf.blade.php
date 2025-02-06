<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Relatório de Prestação de Contas</title>

  <!-- Se seu PDF gerar corretamente com CDN, use o link abaixo -->
  <!-- Caso contrário, baixe o arquivo bootstrap.min.css localmente e referencie-o via arquivo local -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    integrity="sha384-ENjdO4Dr2bkBIFxQpe5Of4nZBj2hRF4yNT0fE259M1N5ezbKEZ9iR4+uew2bLT2y"
    crossorigin="anonymous"
  >

  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px; /* Ajuste global de fonte */
    }

    /* Fallback para tabela caso o Bootstrap externo não seja aplicado pelo gerador de PDF */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    table th, table td {
      border: 1px solid #ccc;
      padding: 8px;
      vertical-align: middle;
    }
    table thead th {
      background-color: #f8f9fa; /* corzinha de cabeçalho */
    }

    /* Ajuste da logo */
    .logo {
      width: 100px;
      height: auto;
      margin-bottom: 10px;
    }

    /* Centralização opcional de elementos nos cabeçalhos */
    .text-center {
      text-align: center;
    }
    .text-right {
      text-align: right;
    }
    .mt-4 {
      margin-top: 1.5rem;
    }
    .mb-4 {
      margin-bottom: 1.5rem;
    }
    .mb-3 {
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>

  <div class="container">

    <!-- Cabeçalho da Empresa -->
    <div class="text-center mb-4">
      <hr>
        @if (!empty($company->avatar))
          <img class="logo" src="{{ public_path('storage/' . $company->avatar) }}" alt="Logo">
        @else
          <img class="logo" src="/assets/media/png/perfil.svg" alt="Logo">
        @endif

        <h2 class="h5 m-0">{{ $company->name }}</h2>
        <p class="m-0">CNPJ: {{ $company->cnpj }}</p>

        <p class="m-0">
          {{ $company->addresses->rua ?? '' }},
          {{ $company->addresses->bairro ?? '' }} -
          {{ $company->addresses->cidade ?? '' }}/{{ $company->addresses->uf ?? '' }}
        </p>
        <p class="m-0">
          Fone: {{ $company->addresses->bairro ?? '' }}
          - E-mail: {{ $company->email }}
        </p>
      <hr>
    </div>

    <!-- Título do relatório e período -->
    <div class="mb-4 text-center">
      <h2 class="h5">RELATÓRIO DE PRESTAÇÃO DE CONTAS</h2>
      <p class="m-0"><strong>Período:</strong> {{ $dataInicial }} - {{ $dataFinal }}</p>
    </div>

    <!-- Centro de Custo, se houver -->
    @if ($costCenter)
      <p><strong>Centro de Custo:</strong> {{ $costCenter }}</p>
    @endif

    <!-- Loop das categorias / tipos de documentos -->
    @foreach ($dados as $categoria)
      <h3 class="h6 mb-3">
        Conta Contábil / Tipo Documento: {{ $categoria['tipo_documento'] }}
      </h3>

      <!-- Tabela de movimentações -->
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Data</th>
            <th>Entidade</th>
            <th>Descrição</th>
            <th class="text-right">Entrada</th>
            <th class="text-right">Saída</th>
            <th class="text-right">Saldo</th>
          </tr>
        </thead>
        <tbody>
          @php $saldoAcumulado = 0; @endphp

          @foreach ($categoria['movimentacoes'] as $mov)
            @php
              $entrada = $mov->tipo === 'E' ? $mov->valor : 0;
              $saida   = $mov->tipo === 'S' ? $mov->valor : 0;
              $saldoAcumulado = $saldoAcumulado + ($entrada - $saida);
            @endphp
            <tr>
              <td>{{ \Carbon\Carbon::parse($mov->data_competencia)->format('d/m/Y') }}</td>
              <td>{{ $mov->entidade_id }}</td>
              <td>{{ $mov->descricao }}</td>
              <td class="text-right">
                {{ $entrada > 0 ? number_format($entrada, 2, ',', '.') : '' }}
              </td>
              <td class="text-right">
                {{ $saida > 0 ? number_format($saida, 2, ',', '.') : '' }}
              </td>
              <td class="text-right">
                {{ number_format($saldoAcumulado, 2, ',', '.') }}
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <!-- Exemplo de rodapé da tabela, pode adaptar para o que você precisa -->
          <tr>
            <th scope="row" colspan="3">Subtotal / Exemplo</th>
            <td class="text-right">45</td>
            <td class="text-right">50</td>
            <td class="text-right">33</td>
          </tr>
        </tfoot>
      </table>

      <!-- Totais da categoria -->
      <p>
        <strong>Total Entradas:</strong>
        R$ {{ number_format($categoria['total_entrada'], 2, ',', '.') }}
      </p>
      <p>
        <strong>Total Saídas:</strong>
        R$ {{ number_format($categoria['total_saida'], 2, ',', '.') }}
      </p>

      <hr>
    @endforeach
  </div>

  <!-- Script JS do Bootstrap (opcional)
       Se seu PDF não processar JS, pode remover. -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+4hZl5P4x63n4J1AHAy0WrPp9U+3u"
    crossorigin="anonymous"
  ></script>

</body>
</html>
