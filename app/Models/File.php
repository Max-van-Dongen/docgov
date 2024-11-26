<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    protected $fillable = [
        'location',
        'summary',
        'title',
    ];


    public function people()
    {
        return $this->belongsToMany(Person::class, 'pdf_people');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'pdf_keywords');
    }

}

