<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShareRequestInfo extends Model
{
    protected $table = 'sharerequestinfos';
    protected $fillable = ['id', 'sharerequestName', 'sharerequestedName'];
}
