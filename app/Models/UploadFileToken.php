<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadFileToken extends Model
{
    protected $guarded = false;

    // status
    const STATUS_PENDING = 'pending';
    const STATUS_UPLOADED = 'uploaded';
    const STATUS_EXPIRED = 'expired';
}
