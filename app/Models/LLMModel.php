<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LLMModel extends Model
{

    protected $table = 'llm_models';

    protected $fillable = [
        'name',
        'is_generating'
    ];
}
