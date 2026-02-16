<?php

namespace Modules\Service\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Service\Models\ServiceTrainingDurationMapping;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class ServiceTraining extends BaseModel
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $table = 'service_training';
    protected $fillable = ['name', 'slug', 'status', 'description','created_by'];
    
    protected static function newFactory()
    {
        return \Modules\Service\Database\factories\ServiceTrainingFactory::new();
    }
    public function trainingDuration()
    {
        return $this->hasMany(ServiceTrainingDurationMapping::class, 'type_id', 'id');
    }
    protected static function boot()
    {
        parent::boot();

        // create a event to happen on creating
        static::creating(function ($table) {
            //
        });

        static::saving(function ($table) {
            //
        });

        static::updating(function ($table) {
            //
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
