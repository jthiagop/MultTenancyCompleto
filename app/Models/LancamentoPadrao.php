<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class LancamentoPadrao extends Model
{
    use HasFactory;

    protected $table = 'lancamento_padraos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'type',
        'description',
        'category',
        'user_id',
        'company_id',
        'conta_debito_id',
        'conta_credito_id',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    // Relacionamento com o usuário que criou o lançamento padrão
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com caixas que utilizam este lançamento padrão
    public function caixas()
    {
        return $this->hasMany(Caixa::class, 'lancamento_padrao_id');
    }

    // Relacionamento com bancos que utilizam este lançamento padrão
    public function bancos()
    {
        return $this->hasMany(Banco::class, 'lancamento_padrao_id');
    }

    // Relacionamento com conta de débito (contabilidade)
    public function contaDebito()
    {
        return $this->belongsTo(\App\Models\Contabilide\ChartOfAccount::class, 'conta_debito_id');
    }

    // Relacionamento com conta de crédito (contabilidade)
    public function contaCredito()
    {
        return $this->belongsTo(\App\Models\Contabilide\ChartOfAccount::class, 'conta_credito_id');
    }

        /**
     * Retorna o emoji correspondente à categoria.
     *
     * @return string
     */
    public function getCategoryEmoji(): string
    {
        $emojis = [
            'Administrativo' => '🏢',
            'Alimentação' => '🍴',
            'Cerimônias' => '🎉',
            'Comércio' => '🛒',
            'Coletas' => '🗑️',
            'Comunicação' => '📞',
            'Contribuições' => '💰',
            'Doações' => '🎁',
            'Educação' => '📚',
            'Equipamentos' => '🛠️',
            'Eventos' => '🎪',
            'Intenções' => '🙏',
            'Liturgia' => '⛪',
            'Manutenção' => '🔧',
            'Material de escritório' => '📎',
            'Pessoal' => '👤',
            'Rendimentos' => '💹',
            'Saúde' => '🏥',
            'Serviços essenciais' => '⚙️',
            'Suprimentos' => '📦',
            'Financeiro' => '💳',
            'Transporte' => '🚗',
        ];

        return $emojis[$this->category] ?? '❓'; // Retorna '❓' se a categoria não for encontrada
    }

        /**
     * Scope: Filtra a busca para incluir apenas os registros da empresa ativa na sessão.
     * Este é o método que estava faltando.
     */
    public function scopeForActiveCompany($query)
    {
        // Pega o ID da empresa que está na sessão
        $activeCompanyId = session('active_company_id');

        // Se houver uma empresa ativa, aplica o filtro.
        if ($activeCompanyId) {
            return $query->where('company_id', $activeCompanyId);
        }

        // Se não houver, retorna uma consulta que não trará resultados para proteger os dados.
        return $query->whereRaw('1 = 0');
    }
}
