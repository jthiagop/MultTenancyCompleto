<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Módulo Financeiro
            [
                'name' => 'financeiro.index',
                'description' => 'Visualizar listagem do módulo financeiro'
            ],
            [
                'name' => 'financeiro.create',
                'description' => 'Criar registros no módulo financeiro'
            ],
            [
                'name' => 'financeiro.edit',
                'description' => 'Editar registros do módulo financeiro'
            ],
            [
                'name' => 'financeiro.delete',
                'description' => 'Excluir registros do módulo financeiro'
            ],
            [
                'name' => 'financeiro.show',
                'description' => 'Visualizar detalhes de registros do módulo financeiro'
            ],

            // Módulo Patrimônio
            [
                'name' => 'patrimonio.index',
                'description' => 'Visualizar listagem do módulo patrimônio'
            ],
            [
                'name' => 'patrimonio.create',
                'description' => 'Criar registros no módulo patrimônio'
            ],
            [
                'name' => 'patrimonio.edit',
                'description' => 'Editar registros do módulo patrimônio'
            ],
            [
                'name' => 'patrimonio.delete',
                'description' => 'Excluir registros do módulo patrimônio'
            ],
            [
                'name' => 'patrimonio.show',
                'description' => 'Visualizar detalhes de registros do módulo patrimônio'
            ],

            // Módulo Contabilidade
            [
                'name' => 'contabilidade.index',
                'description' => 'Visualizar listagem do módulo contabilidade'
            ],
            [
                'name' => 'contabilidade.plano-contas.index',
                'description' => 'Visualizar listagem do plano de contas'
            ],
            [
                'name' => 'contabilidade.plano-contas.create',
                'description' => 'Criar contas no plano de contas'
            ],
            [
                'name' => 'contabilidade.plano-contas.edit',
                'description' => 'Editar contas do plano de contas'
            ],
            [
                'name' => 'contabilidade.plano-contas.delete',
                'description' => 'Excluir contas do plano de contas'
            ],
            [
                'name' => 'contabilidade.plano-contas.import',
                'description' => 'Importar plano de contas'
            ],
            [
                'name' => 'contabilidade.plano-contas.export',
                'description' => 'Exportar plano de contas'
            ],
            [
                'name' => 'contabilidade.mapeamento.index',
                'description' => 'Visualizar mapeamentos DE/PARA'
            ],
            [
                'name' => 'contabilidade.mapeamento.store',
                'description' => 'Criar mapeamentos DE/PARA'
            ],
            [
                'name' => 'contabilidade.mapeamento.delete',
                'description' => 'Excluir mapeamentos DE/PARA'
            ],

            // Módulo Fiéis
            [
                'name' => 'fieis.index',
                'description' => 'Visualizar listagem de fiéis'
            ],
            [
                'name' => 'fieis.create',
                'description' => 'Criar registros de fiéis'
            ],
            [
                'name' => 'fieis.edit',
                'description' => 'Editar registros de fiéis'
            ],
            [
                'name' => 'fieis.delete',
                'description' => 'Excluir registros de fiéis'
            ],
            [
                'name' => 'fieis.show',
                'description' => 'Visualizar detalhes de fiéis'
            ],

            // Módulo Cemitério
            [
                'name' => 'cemiterio.index',
                'description' => 'Visualizar listagem do módulo cemitério'
            ],
            [
                'name' => 'cemiterio.create',
                'description' => 'Criar registros no módulo cemitério'
            ],
            [
                'name' => 'cemiterio.edit',
                'description' => 'Editar registros do módulo cemitério'
            ],
            [
                'name' => 'cemiterio.delete',
                'description' => 'Excluir registros do módulo cemitério'
            ],
            [
                'name' => 'cemiterio.show',
                'description' => 'Visualizar detalhes de registros do módulo cemitério'
            ],

            // Módulo NF-e (Nota Fiscal)
            [
                'name' => 'notafiscal.index',
                'description' => 'Visualizar listagem do módulo nota fiscal'
            ],
            [
                'name' => 'notafiscal.create',
                'description' => 'Criar registros no módulo nota fiscal'
            ],
            [
                'name' => 'notafiscal.edit',
                'description' => 'Editar registros do módulo nota fiscal'
            ],
            [
                'name' => 'notafiscal.delete',
                'description' => 'Excluir registros do módulo nota fiscal'
            ],
            [
                'name' => 'notafiscal.show',
                'description' => 'Visualizar detalhes de registros do módulo nota fiscal'
            ],

            // Módulo Dízimo e Doações
            [
                'name' => 'dizimos.index',
                'description' => 'Visualizar listagem de dízimos e doações'
            ],
            [
                'name' => 'dizimos.create',
                'description' => 'Criar registros de dízimos e doações'
            ],
            [
                'name' => 'dizimos.edit',
                'description' => 'Editar registros de dízimos e doações'
            ],
            [
                'name' => 'dizimos.delete',
                'description' => 'Excluir registros de dízimos e doações'
            ],
            [
                'name' => 'dizimos.show',
                'description' => 'Visualizar detalhes de dízimos e doações'
            ],

            // Módulo Secretaria
            [
                'name' => 'secretary.index',
                'description' => 'Visualizar listagem da secretaria'
            ],
            [
                'name' => 'secretary.create',
                'description' => 'Criar registros na secretaria'
            ],
            [
                'name' => 'secretary.edit',
                'description' => 'Editar registros da secretaria'
            ],
            [
                'name' => 'secretary.delete',
                'description' => 'Excluir registros da secretaria'
            ],
            [
                'name' => 'secretary.show',
                'description' => 'Visualizar detalhes de registros da secretaria'
            ],

            // Módulo Organismos (Company)
            [
                'name' => 'company.index',
                'description' => 'Visualizar listagem de organismos/empresas'
            ],
            [
                'name' => 'company.create',
                'description' => 'Criar organismos/empresas'
            ],
            [
                'name' => 'company.edit',
                'description' => 'Editar organismos/empresas'
            ],
            [
                'name' => 'company.delete',
                'description' => 'Excluir organismos/empresas'
            ],
            [
                'name' => 'company.show',
                'description' => 'Visualizar detalhes de organismos/empresas'
            ],

            // Módulo Usuários
            [
                'name' => 'users.index',
                'description' => 'Visualizar listagem de usuários'
            ],
            [
                'name' => 'users.create',
                'description' => 'Criar usuários'
            ],
            [
                'name' => 'users.edit',
                'description' => 'Editar usuários'
            ],
            [
                'name' => 'users.delete',
                'description' => 'Excluir usuários'
            ],
            [
                'name' => 'users.show',
                'description' => 'Visualizar detalhes de usuários'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                ['name' => $permission['name'], 'guard_name' => 'web']
            );
            // Nota: Se a tabela permissions tiver coluna 'description', adicione aqui
            // Atualmente o Spatie Permission não tem coluna description por padrão
        }
    }
}

