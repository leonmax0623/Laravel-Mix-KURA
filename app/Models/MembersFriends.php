<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembersFriends extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'members_friends';
    public $timestamps = false;//если created_at, updated_at нету

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'first_member_id',
        'second_member_id'
    ];

    use HasFactory;
}
