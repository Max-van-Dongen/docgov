<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    private $protected = ['id'];

    protected $fillable = [
        'word',
    ];
    public function files()
    {
        return $this->belongsToMany(File::class, 'pdf_keywords');
    }

}
