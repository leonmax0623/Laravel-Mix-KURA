<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'messages';
    public $timestamps = false;//если created_at, updated_at нету

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'lesson_id',
        'parent_id',
        'from_id',
        'to_id',
        'text',
        'send_date',
        'opened_date',
        'opened',
        'message_type',
        'deleted'
    ];

    use HasFactory;
}
