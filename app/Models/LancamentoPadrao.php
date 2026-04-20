<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompanyHierarchy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

use App\Models\User;

class LancamentoPadrao extends Model
{
    use HasFactory;
    use BelongsToCompanyHierarchy;

    protected $table = 'lancamento_padraos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * Nome da tabela pivot usada pelo trait BelongsToCompanyHierarchy.
     */
    protected string $companyPivotTable = 'lancamento_padrao_company';

    protected $fillable = [
        'type',
        'codigo',
        'description',
        'category',
        'user_id',
        'is_active',
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
     * 1. O Laravel usa isso para gerar a URL (route('lancamentoPadrao.show', $lancamento))
     */
    public function getRouteKey()
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * 2. O Laravel usa isso para encontrar o model vindo da URL
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = Hashids::decode($value);
        return $this->where('id', $decoded[0] ?? null)->firstOrFail();
    }
}
