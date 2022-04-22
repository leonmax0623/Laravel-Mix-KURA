<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'members';

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'email',
        'phone',
        'password',
        'password_changed',
        'auth_hash',
        'tariff_id',
        'blocked',
        'dev_key',
        'role',
        'created_at',
        'updated_at'
    ];

    use HasFactory;
}
