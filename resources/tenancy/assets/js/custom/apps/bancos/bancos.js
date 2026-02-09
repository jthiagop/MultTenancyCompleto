    document.addEventListener('DOMContentLoaded', function() {
        const bancoList = {
            '001': { name: 'Banco do Brasil S.A', svg: '/tenancy/assets/media/svg/bancos/brasil.svg' },
            '033': { name: 'Banco Santander Brasil S.A', svg: '/tenancy/assets/media/svg/bancos/santander.svg' },
            '237': { name: 'Bradesco S.A', svg: '/tenancy/assets/media/svg/bancos/bradesco.svg' },
            '104': { name: 'Caixa Econômica Federal', svg: '/tenancy/assets/media/svg/bancos/caixa.svg' },
            '341': { name: 'Itaú Unibanco S.A', svg: '/tenancy/assets/media/svg/bancos/itau.svg' },
            '143': { name: 'Lets Bank S.A', svg: '/tenancy/assets/media/svg/bancos/lets.svg' },
            '403': { name: 'Mercado Pago', svg: '/tenancy/assets/media/svg/bancos/mercadopago.svg' },
            '260': { name: 'Nu Pagamentos S.A (Nubank)', svg: '/tenancy/assets/media/svg/bancos/nubank.svg' },
            '136': { name: 'Unicred', svg: '/tenancy/assets/media/svg/bancos/unicred.svg' },
            '290': { name: 'PagSeguro Internet S.A', svg: '/tenancy/assets/media/svg/bancos/pagseguro.svg' },
            '748': { name: 'Sicredi', svg: '/tenancy/assets/media/svg/bancos/sicredi.svg' },
            '197': { name: 'Stone Pagamentos S.A', svg: '/tenancy/assets/media/svg/bancos/stone.svg' },
            '065': { name: 'Ailos', svg: '/tenancy/assets/media/svg/bancos/ailos.svg' },
            '756': { name: 'Sicoob', svg: '/tenancy/assets/media/svg/bancos/sicoob.svg' },
            '000': { name: 'Quality Digital Bank - temporária', svg: '/tenancy/assets/media/svg/bancos/qualidade.svg' },
            '364': { name: 'Asaas IP S.A', svg: '/tenancy/assets/media/svg/bancos/assas.svg' },
            '070': { name: 'BRB - Banco de Brasília', svg: '/tenancy/assets/media/svg/bancos/brasilia.svg' },
            '218': { name: 'Banco BS2 S.A', svg: '/tenancy/assets/media/svg/bancos/bs2.svg' },
            '208': { name: 'Banco BTG Pactual', svg: '/tenancy/assets/media/svg/bancos/btg.svg' },
            '336': { name: 'Banco C6 S.A', svg: '/tenancy/assets/media/svg/bancos/c6.svg' },
            '707': { name: 'Banco Daycoval', svg: '/tenancy/assets/media/svg/bancos/daycoval.svg' },
            '604': { name: 'Banco Industrial do Brasil S.A', svg: '/tenancy/assets/media/svg/bancos/industrial.svg' },
            '077': { name: 'Banco Inter S.A', svg: '/tenancy/assets/media/svg/bancos/inter.svg' },
            '389': { name: 'Banco Mercantil do Brasil S.A', svg: '/tenancy/assets/media/svg/bancos/mercantil.svg' },
            '212': { name: 'Banco Original S.A', svg: '/tenancy/assets/media/svg/bancos/original.svg' },
            '643': { name: 'Banco Pine', svg: '/tenancy/assets/media/svg/bancos/pine.svg' },
            '633': { name: 'Banco Rendimento', svg: '/tenancy/assets/media/svg/bancos/rendimento.svg' },
            '422': { name: 'Banco Safra S.A', svg: '/tenancy/assets/media/svg/bancos/safra.svg' },
            '637': { name: 'Banco Sofisa', svg: '/tenancy/assets/media/svg/bancos/sofisa.svg' },
            '082': { name: 'Banco Topazio', svg: '/tenancy/assets/media/svg/bancos/topazio.svg' },
            '634': { name: 'Banco Triângulo - Tribanco', svg: '/tenancy/assets/media/svg/bancos/triangulo.svg' },
            '003': { name: 'Banco da Amazônia S.A', svg: '/tenancy/assets/media/svg/bancos/amazonia.svg' },
            '021': { name: 'Banco do Estado do Espírito Santo', svg: '/tenancy/assets/media/svg/bancos/espirito-santo.svg' },
            '037': { name: 'Banco do Estado do Pará', svg: '/tenancy/assets/media/svg/bancos/para.svg' },
            '047': { name: 'Banco do Estado do Sergipe', svg: '/tenancy/assets/media/svg/bancos/sergipe.svg' },
            '004': { name: 'Banco do Nordeste do Brasil S.A', svg: '/tenancy/assets/media/svg/bancos/nordeste.svg' },
            '011': { name: 'Bank of America', svg: '/tenancy/assets/media/svg/bancos/america.svg' },
            '041': { name: 'Banrisul', svg: '/tenancy/assets/media/svg/bancos/banrisul.svg' },
            '268': { name: 'Capitual', svg: '/tenancy/assets/media/svg/bancos/capitual.svg' },
            '331': { name: 'Conta Simples Soluções em Pagamentos', svg: '/tenancy/assets/media/svg/bancos/conta-simples.svg' },
            '323': { name: 'Cora Sociedade Crédito Direto S.A', svg: '/tenancy/assets/media/svg/bancos/cora.svg' },
            '097': { name: 'Credisis', svg: '/tenancy/assets/media/svg/bancos/credisis.svg' },
            '085': { name: 'Cresol', svg: '/tenancy/assets/media/svg/bancos/cresol.svg' },
            '401': { name: 'Grafeno', svg: '/tenancy/assets/media/svg/bancos/grafeno.svg' },
            '084': { name: 'Uniprime', svg: '/tenancy/assets/media/svg/bancos/uniprime.svg' }
        };



        const bancoCells = document.querySelectorAll('td[data-banco-code]');
        bancoCells.forEach(cell => {
            const bancoCode = cell.getAttribute('data-banco-code');
            const bancoInfo = bancoList[bancoCode] || bancoList['000'];
            cell.querySelector('img').src = bancoInfo.svg;
            cell.querySelector('img').alt = bancoInfo.name;
            cell.querySelector('.banco-name').textContent = bancoInfo.name;
        });

    });
