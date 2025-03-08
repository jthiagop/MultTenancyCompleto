<?php

namespace App\Console\Commands;

use App\Models\ContasFinanceiras;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AtualizarStatusContas extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contas:atualizar-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o status de contas vencidas de "em aberto" para "vencida"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
                // Atualiza diretamente sem precisar recuperar cada registro
                $quantidade = ContasFinanceiras::where('status_pagamento', 'em aberto')
                ->whereDate('data_primeiro_vencimento', '<', Carbon::today())
                ->update(['status_pagamento' => 'vencida']);

            $this->info("{$quantidade} contas atualizadas para vencida.");
    }
}
