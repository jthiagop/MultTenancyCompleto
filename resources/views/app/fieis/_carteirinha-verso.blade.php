{{--
    Partial reutilizável: VERSO da carteirinha do dizimista (controle de
    dízimos com 12 meses + checkbox + data + valor em branco).

    Variáveis esperadas no escopo:
      - $ano (int)

    O CSS das classes (.card, .header, .controle, table.meses, .footer)
    é definido no template pai.
--}}
@php
    $mesesPartial = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
    ];
    $colunaA = array_slice($mesesPartial, 0, 6);
    $colunaB = array_slice($mesesPartial, 6, 6);
@endphp

<div class="card">
    <div class="header">
        <span class="organismo" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
            Controle de Dízimos
        </span>
        <span class="codigo">{{ $ano }}</span>
    </div>

    <div class="controle">
        @foreach ([$colunaA, $colunaB] as $coluna)
            <table class="meses">
                <thead>
                    <tr>
                        <th>Mês</th>
                        <th class="center">✓</th>
                        <th>Data</th>
                        <th class="right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coluna as $mes)
                        <tr>
                            <td class="mes">{{ $mes }}</td>
                            <td class="check"><span class="box"></span></td>
                            <td class="data"></td>
                            <td class="valor"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>

    <div class="footer">
        Marque o mês contribuído, anote a data do pagamento e o valor.
    </div>
</div>
