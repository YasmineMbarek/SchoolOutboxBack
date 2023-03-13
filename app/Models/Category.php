<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;


class Category extends Model
{
    use HasFactory, Notifiable;

    const DEFAULT_CATEGORY = 'Other';

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
