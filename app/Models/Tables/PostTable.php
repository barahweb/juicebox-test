<?php

namespace App\Models\Tables;

use App\Models\Entity\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class PostTable extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
