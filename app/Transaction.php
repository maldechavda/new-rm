<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $dates = [
        'date'
    ];

    protected $fillable = [
        'account_id', 'sequence_number', 'date', 'description', 'reference', 'credit_amount', 'debit_amount',
        'closing_balance', 'type', 'note', 'invoice'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
