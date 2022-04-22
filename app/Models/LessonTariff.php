<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonTariff extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'lesson_tariff';
    public $timestamps = false;//если created_at, updated_at нету

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'lesson_id',
        'tariff_id'
    ];

    use HasFactory;
}
