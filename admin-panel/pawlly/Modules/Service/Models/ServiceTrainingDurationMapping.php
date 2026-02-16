<?php

namespace Modules\Service\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use  Modules\Service\Models\ServiceTraining;

class ServiceTrainingDurationMapping extends Model
{
    use HasFactory;

    protected $table = 'service_training_duration_mapping';

    protected $fillable = [
        'type_id', 'duration', 'amount','status'
    ];

    protected $casts = [
        'type_id' => 'integer',
        'amount' => 'double',
    ];

    public function servicetraining()
    {
        return $this->belongsTo(ServiceTraining::class, 'type_id', 'id');
    }
    
    protected static function newFactory()
    {
        return \Modules\Service\Database\factories\ServiceTrainingDurationMappingFactory::new();
    }
}
