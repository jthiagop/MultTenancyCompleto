document.addEventListener('DOMContentLoaded', function() {
    const bancoList = {
        '001': { name: 'Banco do Brasil S.A' },
        '033': { name: 'Banco Santander Brasil S.A' },
        '237': { name: 'Bradesco S.A' },
        '104': { name: 'Caixa Econômica Federal' },
        '341': { name: 'Itaú Unibanco S.A' },
        '143': { name: 'Lets Bank S.A' },
        '403': { name: 'Mercado Pago' },
        '260': { name: 'Nu Pagamentos S.A (Nubank)' },
        '136': { name: 'Unicred' },
        '290': { name: 'PagSeguro Internet S.A' },
        '748': { name: 'Sicredi' },
        '197': { name: 'Stone Pagamentos S.A' },
        '065': { name: 'Ailos' },
        '756': { name: 'Sicoob' },
        '000': { name: 'Quality Digital Bank - temporária' },
        '364': { name: 'Asaas IP S.A' },
        '070': { name: 'BRB - Banco de Brasília' },
        '218': { name: 'Banco BS2 S.A' },
        '208': { name: 'Banco BTG Pactual' },
        '336': { name: 'Banco C6 S.A' },
        '707': { name: 'Banco Daycoval' },
        '604': { name: 'Banco Industrial do Brasil S.A' },
        '077': { name: 'Banco Inter S.A' },
        '389': { name: 'Banco Mercantil do Brasil S.A' },
        '212': { name: 'Banco Original S.A' },
        '643': { name: 'Banco Pine' },
        '633': { name: 'Banco Rendimento' },
        '422': { name: 'Banco Safra S.A' },
        '637': { name: 'Banco Sofisa' },
        '082': { name: 'Banco Topazio' },
        '634': { name: 'Banco Triângulo - Tribanco' },
        '003': { name: 'Banco da Amazônia S.A' },
        '021': { name: 'Banco do Estado do Espírito Santo' },
        '037': { name: 'Banco do Estado do Pará' },
        '047': { name: 'Banco do Estado do Sergipe' },
        '004': { name: 'Banco do Nordeste do Brasil S.A' },
        '011': { name: 'Bank of America' },
        '041': { name: 'Banrisul' },
        '268': { name: 'Capitual' },
        '331': { name: 'Conta Simples Soluções em Pagamentos' },
        '323': { name: 'Cora Sociedade Crédito Direto S.A' },
        '097': { name: 'Credisis' },
        '085': { name: 'Cresol' },
        '401': { name: 'Grafeno' },
        '084': { name: 'Uniprime' }
    };

    // Inicializa o select2
    $('#banco-select').select2({
        placeholder: "Escolha um banco...",
        allowClear: true
    });

    // Atualiza o nome do banco quando selecionado
    $('#banco-select').on('change', function() {
        const selectedCode = $(this).val();
        if (selectedCode && bancoList[selectedCode]) {
            const bancoInfo = bancoList[selectedCode];
            document.getElementById('banco-nome').textContent = bancoInfo.name;
        } else {
            document.getElementById('banco-nome').textContent = '';
        }
    });
});
