<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

    protected $table = "modules";

    protected $guarded = ['id'];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
