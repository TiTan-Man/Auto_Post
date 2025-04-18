<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = ['scenario_id', 'text_content', 'image_url','facebook_post_id','page_id'];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }
}