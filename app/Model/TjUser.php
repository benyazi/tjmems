<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TjUser extends Model
{
    protected $table = 'tj_users';
    protected $fillable = [
        'tjId',
        'created',
        'name',
        'avatarUrl',
        'entryCount',
        'commentCount',
        'favoriteCount',
        'isAdmin',
        'isSubscriptionActive',
        'tjObject'
        ];

}

