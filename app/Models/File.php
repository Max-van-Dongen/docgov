<?php

namespace App\Models;

use Illuminate\Container\Attributes\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    protected $fillable = [
        'location',
        'summary',
        'title',
        'short_desc',
        'original_date',
        'type_document',
        'type_category',
    ];


    public function people()
    {
        return $this->belongsToMany(Person::class, 'pdf_people');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'pdf_keywords');
    }

    public function getUrlAttribute() {
        return str_contains($this->location,"https")!==false ? "/load-pdf?url=".$this->location : Storage::url($this->location);
    }

}

