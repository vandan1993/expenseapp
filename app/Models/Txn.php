<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Txn extends Model
{
    use HasFactory;

    protected $table  = 'txn';

    protected $fillable = [
        'txn_reference',
        'user_id',
        'amount',
        'txn_details',
        'expense',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user()
    {
       return $this->belongsTo(User::class , 'user_id' , 'id');
    }

    public function txnMetaViaTxnRefno()
    {
        return $this->hasMany(TxnMeta::class , 'txn_reference' , 'txn_reference');
    }

    public function txnMetaViaUserId()
    {
        return $this->hasMany(TxnMeta::class , 'user_id' , 'user_id');
    }

}
