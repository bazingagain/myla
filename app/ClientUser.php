<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ClientUser
 *
 * @property integer $id
 * @property string $clientName
 * @property string $password
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\ClientUser whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClientUser whereClientName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClientUser wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClientUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClientUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClientUser extends Model
{
    /**
     * 可以被批量赋值的属性
     *
     * @var array
     */
    protected $fillable = ['clientName', 'password'];

}
