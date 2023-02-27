<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Post extends Model {
    use HasFactory, HasApiTokens;

    protected $table = 'posts';

    protected $fillable = ['title', 'summary', 'content', 'slug', 'thumbnail', 'views', 'category', 'writer'];


    public function writer() {
        return $this->hasOne(User::class, 'id', 'writer');
    }

    public function category() {
        return $this->hasOne(Category::class, 'id', 'category');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

}
