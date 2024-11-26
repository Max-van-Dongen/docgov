<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    private $protected = ['id'];
    protected $fillable = [
        'name',
    ];
    public function files()
    {
        return $this->belongsToMany(File::class, 'pdf_people');
    }

}
