<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable =   [
        'telegram_id',
        ];

    public function carPreferences(): HasMany
    {
        return $this->hasMany(CarPreference::class, foreignKey: 'telegram_id');
    }

}
