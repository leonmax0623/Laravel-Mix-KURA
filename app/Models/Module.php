<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'module';
    public $timestamps = false;//если created_at, updated_at нету

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'name',
        'description',
        'img_url',
        'deleted'
    ];

    use HasFactory;
}
