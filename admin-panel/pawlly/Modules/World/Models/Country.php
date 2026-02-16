<?php

namespace Modules\World\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'countries';

    protected $fillable = ['name', 'status'];

    protected $casts = [
        'status' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\World\Database\factories\CountryFactory::new();
    }

    /**
     * Boot method to add event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($country) {
            if (Country::where('name', $country->name)->exists()) {
                throw new \Exception('The name must be unique.');
            }
        });

        static::updating(function ($country) {
            if (Country::where('name', $country->name)
                ->where('id', '!=', $country->id)
                ->exists()) {
                throw new \Exception('The name must be unique.');
            }
        });
    }
}
