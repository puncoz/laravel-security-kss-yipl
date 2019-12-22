<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * @package App
 *
 * @property int    $id
 * @property string $name
 * @property string $message
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Message extends Model
{
    protected $fillable = [
        'name',
        'message',
    ];
}
