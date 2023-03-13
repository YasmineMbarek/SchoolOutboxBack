<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;


class Article extends Model
{
    use HasFactory, Notifiable;

    const STATUS_CREATED = 'created';
    const STATUS_RECEIVED = 'received';
    const STATUS_AFFECTED = 'affected';


    protected $fillable = [
        'customer_id',
        'category_id',
        'name',
        'deposit_date',
        'status',
        'description',
        'state'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class, 'article_id');

    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function demands(): HasMany
    {
        return $this->hasMany(Demand::class);

    }
}
