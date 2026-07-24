<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'level',
        'message',
        'stack_trace',
        'ai_summary',
        'status',
    ];
}