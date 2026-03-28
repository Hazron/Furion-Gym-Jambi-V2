<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class fingerprints extends Model
{
    protected $table = 'fingerprints';

    protected $fillable = [
        'member_id',
        'fingerprint_template',
        'device_id',
        'last_updated',
    ];
}
