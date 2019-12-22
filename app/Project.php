<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Project
 * @package App
 *
 * @property int     $id
 * @property string  $title
 * @property string  $description
 * @property boolean $is_started
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 */
class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_started',
    ];

    protected $casts = [
        'is_started' => 'boolean',
    ];
}
