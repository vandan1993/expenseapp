<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TxnMeta extends Model
{
    use HasFactory;

    protected $table  = 'txn_meta';

    protected $fillable = [
        'txn_reference',
        'user_id',
        'split_amount',
        'split_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public static function collectFromUser($user_id)
    {
        return TxnMeta::join('users', 'txn_meta.split_user_id', '=', 'users.id')
        ->selectRaw('split_user_id ,SUM(split_amount) as split_amount , name')
        ->whereColumn('split_user_id' , '<>' , 'user_id')
        ->where('user_id' , $user_id)
        ->groupBy('split_user_id')
        ->orderBy('split_user_id')->get()->toArray();
    }

    public static function owesToUser($user_id = null)
    {
        return TxnMeta::join('users', 'txn_meta.user_id', '=', 'users.id')
        ->selectRaw('user_id ,SUM(split_amount) as split_amount , name')
        ->whereColumn('split_user_id' , '<>' , 'user_id')
        ->where('split_user_id' , $user_id)
        ->groupBy('user_id')
        ->orderBy('user_id')->get()->toArray();
    }

    public static function getEveryRecordBalance(){
        return  TxnMeta::join('users AS u', 'txn_meta.user_id', '=', 'u.id')
        ->join('users AS su', 'txn_meta.split_user_id', '=', 'su.id')
        ->selectRaw('user_id , split_user_id ,SUM(split_amount) as split_amount , u.name as user_name ,  su.name')
        ->whereColumn('split_user_id' , '<>' , 'user_id')
        ->groupBy('user_id' , 'split_user_id')
        ->orderBy('user_id')->get()->toArray();
    }
}
