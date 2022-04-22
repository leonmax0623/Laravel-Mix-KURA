<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonFiles extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'lesson_files';
    public $timestamps = false;//если created_at, updated_at нету

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'lesson_id',
        'file_url',
        'file_url_reserve',
        'file_type',
        'sort'
    ];

    use HasFactory;
}
