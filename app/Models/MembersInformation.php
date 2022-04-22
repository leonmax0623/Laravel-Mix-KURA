<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembersInformation extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'members_information';
    public $timestamps = false;//если created_at, updated_at нету

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'member_id',
        'field_name',
        'value',
        'public'
    ];

    use HasFactory;
}
