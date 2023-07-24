<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;
    protected $fillable = ['title_en' , 'title_ar'];

    public function options()
    {
        return $this->hasMany(Option::class);
    }


}
