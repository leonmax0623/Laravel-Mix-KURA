<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleTariff extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'module_tariff';
    public $timestamps = false;//если created_at, updated_at нету

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'module_id',
        'tariff_id',
        'date_open'
    ];

    use HasFactory;
}
