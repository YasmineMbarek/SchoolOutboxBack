<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;


class Picture extends Model
{
    use HasFactory, Notifiable;
    const MAX_PICTURES = 2;
    protected $fillable = [
        'path' ,
        'article_id' ,


    ];
    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
