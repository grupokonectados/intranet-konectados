<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailNotify extends Model
{
    use HasFactory;

    protected $fillable = ['estado'];
}
