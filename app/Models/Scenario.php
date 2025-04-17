<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Content;

class Scenario extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}