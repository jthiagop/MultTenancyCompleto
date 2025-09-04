<?php

namespace Database\Seeders;

use App\Models\FormasPagamento;
use Illuminate\Database\Seeder;

class FormasPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formasPagamento = [
            [
                'nome' => 'Cashback',
                'codigo' => 'CASHBACK',
                'ativo' => true,
                'tipo_taxa' => 'porcentagem',
                'taxa' => 2.50,
                'prazo_liberacao' => 30,
                'metodo_integracao' => 'API',
                'observacao' => 'Cashback em compras com cartão de crédito'
            ],
            [
                'nome' => 'Boleto Via outros bancos',
                'codigo' => 'BOLETO',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 3.50,
                'prazo_liberacao' => 3,
                'metodo_integracao' => 'API Bancária',
                'observacao' => 'Boleto bancário via outros bancos'
            ],
            [
                'nome' => 'Cheque',
                'codigo' => 'CHEQUE',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 5,
                'metodo_integracao' => 'Manual',
                'observacao' => 'Pagamento via cheque'
            ],
            [
                'nome' => 'Cartão de crédito via outros bancos',
                'codigo' => 'CC_OUTROS',
                'ativo' => true,
                'tipo_taxa' => 'porcentagem',
                'taxa' => 3.99,
                'prazo_liberacao' => 30,
                'metodo_integracao' => 'API Gateway',
                'observacao' => 'Cartão de crédito via outros bancos'
            ],
            [
                'nome' => 'Cartão de débito via outros bancos',
                'codigo' => 'CD_OUTROS',
                'ativo' => true,
                'tipo_taxa' => 'porcentagem',
                'taxa' => 1.99,
                'prazo_liberacao' => 1,
                'metodo_integracao' => 'API Gateway',
                'observacao' => 'Cartão de débito via outros bancos'
            ],
            [
                'nome' => 'Carteira Digital',
                'codigo' => 'CARTEIRA',
                'ativo' => true,
                'tipo_taxa' => 'porcentagem',
                'taxa' => 1.50,
                'prazo_liberacao' => 1,
                'metodo_integracao' => 'API Digital',
                'observacao' => 'Pagamento via carteira digital'
            ],
            [
                'nome' => 'Crédito da loja',
                'codigo' => 'CREDITO_LOJA',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'Sistema Interno',
                'observacao' => 'Crédito disponível na loja'
            ],
            [
                'nome' => 'Crédito virtual',
                'codigo' => 'CREDITO_VIRTUAL',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'Sistema Virtual',
                'observacao' => 'Crédito virtual do sistema'
            ],
            [
                'nome' => 'Débito Automático',
                'codigo' => 'DEBITO_AUTO',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'Débito Automático',
                'observacao' => 'Débito automático em conta'
            ],
            [
                'nome' => 'Depósito bancário',
                'codigo' => 'DEPOSITO',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 1,
                'metodo_integracao' => 'API Bancária',
                'observacao' => 'Depósito direto em conta bancária'
            ],
            [
                'nome' => 'Dinheiro',
                'codigo' => 'DINHEIRO',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'Manual',
                'observacao' => 'Pagamento em dinheiro'
            ],
            [
                'nome' => 'Outros',
                'codigo' => 'OUTROS',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'Manual',
                'observacao' => 'Outras formas de pagamento'
            ],
            [
                'nome' => 'Pix',
                'codigo' => 'PIX',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'API PIX',
                'observacao' => 'Pagamento instantâneo via PIX'
            ],
            [
                'nome' => 'Programa de fidelidade',
                'codigo' => 'FIDELIDADE',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'Sistema Fidelidade',
                'observacao' => 'Pontos de fidelidade'
            ],
            [
                'nome' => 'Sem pagamento',
                'codigo' => 'SEM_PAG',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'Sistema Interno',
                'observacao' => 'Transação sem pagamento'
            ],
            [
                'nome' => 'Transferência bancária',
                'codigo' => 'TRANSFERENCIA',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 1,
                'metodo_integracao' => 'API Bancária',
                'observacao' => 'Transferência entre contas'
            ],
            [
                'nome' => 'Vale-alimentação',
                'codigo' => 'VALE_ALIMENTACAO',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'API Vale',
                'observacao' => 'Vale-alimentação'
            ],
            [
                'nome' => 'Vale-combustível',
                'codigo' => 'VALE_COMBUSTIVEL',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'API Vale',
                'observacao' => 'Vale-combustível'
            ],
            [
                'nome' => 'Vale-presente',
                'codigo' => 'VALE_PRESENTE',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'API Vale',
                'observacao' => 'Vale-presente'
            ],
            [
                'nome' => 'Vale-refeição',
                'codigo' => 'VALE_REFEICAO',
                'ativo' => true,
                'tipo_taxa' => 'valor_fixo',
                'taxa' => 0.00,
                'prazo_liberacao' => 0,
                'metodo_integracao' => 'API Vale',
                'observacao' => 'Vale-refeição'
            ]
        ];

        foreach ($formasPagamento as $forma) {
            FormasPagamento::firstOrCreate(
                ['codigo' => $forma['codigo']], // Verifica se já existe pelo código
                $forma
            );
        }

        $this->command->info('Formas de pagamento padrão criadas com sucesso!');
    }
}
