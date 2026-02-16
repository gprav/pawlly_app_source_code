<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Service\Models\ServiceTraining;
use Modules\Service\Models\ServiceDuration;
use Modules\Service\Models\ServiceTrainingDurationMapping;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_training_duration_mapping', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id');
            $table->string('duration')->default(15);
            $table->double('amount')->nullable()->default('0');
            $table->boolean('status')->default(1);
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        $serviceTrainings = ServiceTraining::get();

        foreach ($serviceTrainings as $serviceTraining) {
            $serviceDurations = ServiceDuration::where('type','training')->get();

            foreach ($serviceDurations as $serviceDuration) {
                ServiceTrainingDurationMapping::create([
                    'type_id' => $serviceTraining->id,
                    'duration' => $serviceDuration->duration,
                    'amount' => $serviceDuration->price,
                    'status' => $serviceDuration->status,
                    'created_by' => $serviceDuration->created_by,
                    'updated_by' => $serviceDuration->updated_by,
                    'deleted_by' => $serviceDuration->deleted_by,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_training_duration_mapping');
    }
};
