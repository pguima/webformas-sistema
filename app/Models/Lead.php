<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'whatsapp',
        'plan',
        'services',
        'value',
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
            'payload' => 'array',
        ];
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
}
