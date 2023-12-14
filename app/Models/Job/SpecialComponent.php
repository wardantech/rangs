<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialComponent extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
}
