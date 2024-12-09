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


    /**
     * Get all relevancy records where this file is the primary file.
     */
    public function relevancies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FileRelevancy::class, 'relevant_file_id');
    }

    /**
     * Get all relevancy records where this file is the related file.
     */
    public function relatedRelevancies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FileRelevancy::class, 'relevant_to_file_id');
    }

    /**
     * Get all files related to this file through relevancy.
     */
    public function relatedFiles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            File::class,
            'file_relevancy',
            'relevant_file_id',
            'relevant_to_file_id'
        )
            ->withPivot('relevancy', 'matched_words', 'date_difference_days')
            ->orderBy('date_difference_days', 'asc') // Sort by date_difference_days in ascending order
            ->orderBy('relevancy', 'desc'); // Then sort by relevancy in descending order
    }


}

