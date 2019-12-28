<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * @package App
 *
 * @property int         $id
 * @property string      $email
 * @property string      $name
 * @property Carbon|null $email_verified_at
 * @property string      $password
 * @property string      $display_picture
 * @property string      $remember_token
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 *
 * @property string      $display_picture_url
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'display_picture',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getDisplayPictureUrlAttribute()
    {
        $displayPicture = $this->display_picture;

        try {
            if ( !$displayPicture ) {
                throw new \Exception("Display picture not set.");
            }

            if ( !is_file(public_path("uploads/{$displayPicture}")) ) {
                throw new \Exception("File not found!");
            }

            return asset("uploads/{$displayPicture}");
        } catch (\Exception $exception) {
            return asset("defaults/user.png");
        }
    }
}
