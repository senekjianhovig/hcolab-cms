<?php

namespace hcolab\cms\models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsSentPushNotification extends Model
{
    use HasFactory;

    protected $casts = [
        'dictionary' => 'array'
    ];

}
