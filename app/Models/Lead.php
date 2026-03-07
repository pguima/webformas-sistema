<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Plan;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'whatsapp',
        'cnpj',
        'plan_id',
        'plan',
        'services',
        'service_ids',
        'value',
        'value_base',
        'discount_type',
        'discount_value',
        'value_final',
        'responsible_user_id',
        'origin',
        'campaign',
        'stage',
        'position',
        'external_id',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'value_base' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'value_final' => 'decimal:2',
            'service_ids' => 'array',
            'payload' => 'array',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
}
