<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lessons extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'lessons';
    public $timestamps = false;//если created_at, updated_at нету

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'module_id',
        'name',
        'description',
        'prev_id',
        'next_id',
        'homework',
        'deleted'
    ];

    use HasFactory;
}
