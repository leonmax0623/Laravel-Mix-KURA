<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'tariff';
    public $timestamps = false;//если created_at, updated_at нету

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'name',
        'date_open',
        'date_close',
        'deleted'
    ];

    use HasFactory;
}
