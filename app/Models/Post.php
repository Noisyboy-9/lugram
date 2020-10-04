<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class Post extends Model
{
    protected $fillable = ['image_path'];
}
