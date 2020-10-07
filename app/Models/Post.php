<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class Post extends Model
{
    use HasFactory;

    protected $fillable = ['image_path', 'user_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function path()
    {
        return '/posts/' . $this->id;
    }
}
