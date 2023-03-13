<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Demand extends Model
{
    use HasFactory, Notifiable;

    const STATUS_PENDING = 'pending';
    const STATUS_REFUSED = 'refused';
    const STATUS_ACCEPTED = 'accepted';

    protected $fillable = [
        'article_id' ,
        'customer_id' ,
        'motive',
        'demand_date',
        'status',

    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
