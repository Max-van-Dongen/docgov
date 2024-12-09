<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileRelevancy extends Model
{

    protected $table = 'file_relevancy';

    protected $fillable = [
        'file_id',
        'file2_id',
        'relevancy',
        'date_difference_days',
    ];

    /**
     * Get the file associated with this relevancy record.
     */
    public function file(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(File::class, 'relevant_file_id');
    }

    /**
     * Get the second file associated with this relevancy record.
     */
    public function relatedFile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(File::class, 'relevant_to_file_id');
    }

    public function highlightMatchedWords(string $title, array $matchedWords): string
    {
        foreach ($matchedWords as $word) {
            $title = preg_replace(
                '/\b(' . preg_quote($word, '/') . ')\b/i',
                '<span class="fw-bold">$1</span>',
                $title
            );
        }

        return $title;
    }
}
