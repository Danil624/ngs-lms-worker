<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestAttempt extends Model
{
    protected $fillable = [
        'test_id',
        'user_id',
        'attempt_number',
        'score',
        'percent',
        'passed',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'passed' => 'boolean',
    ];

    public function test()
{
    return $this->belongsTo(Test::class);
}
}