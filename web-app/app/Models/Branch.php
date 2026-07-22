<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Branch extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'governorate',
        'address',
        'phone',
    ];

    public function orphans()
    {
        return $this->hasMany(Orphan::class);
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
