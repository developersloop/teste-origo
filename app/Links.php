<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Links extends Model
{
    use Notifiable;

    protected $fillable = [
        'current_link',
        'short_link',
        'shortened_link',
        'disabled',
        'link_expirate',
        'created_at',
        'updated_at'
    ];

}
