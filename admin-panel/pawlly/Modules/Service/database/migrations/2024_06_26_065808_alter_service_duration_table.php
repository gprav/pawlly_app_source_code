<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Service\Models\ServiceDuration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $serviceDurations = ServiceDuration::get();

        foreach ($serviceDurations as $durationData) {
            $duration = $durationData->duration; // Access duration using arrow notation
            $updatedDuration = preg_replace('/^(\d):/', '0$1:', $duration); // Modify '0:15' to '00:15'
            
            $durationData->update([
                'duration' => $updatedDuration,
                'price' => $durationData->price,
                'type' => $durationData->type,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
        
        // Optionally, ensure all existing 'created_by' fields are set to 1
        ServiceDuration::whereNull('created_by')->update(['created_by' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
