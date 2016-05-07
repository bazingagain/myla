<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddRequestInfo extends Model
{
    protected $table = 'addrequestinfos';
    protected $fillable = ['id', 'addrequestName', 'addrequestedName'];
}
