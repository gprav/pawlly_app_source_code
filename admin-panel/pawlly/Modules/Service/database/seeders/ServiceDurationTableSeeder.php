<?php

namespace Modules\Service\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Service\Models\ServiceDuration;

class ServiceDurationTableSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks!
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /*
         * Services Seed
         * ------------------
         */

        // DB::table('services')->truncate();
        // echo "Truncate: services \n";
        if (env('IS_DUMMY_DATA')) {
            $data = [
                //trianing
                [
                    'duration' => '00:15',
                    'price' => 30,
                    'type' => 'training',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:20',
                    'price' => 25,
                    'type' => 'training',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:25',
                    'price' => 34,
                    'type' => 'training',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:30',
                    'price' => 27,
                    'type' => 'training',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:35',
                    'price' => 15,
                    'type' => 'training',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:40',
                    'price' => 10,
                    'type' => 'training',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:45',
                    'price' => 20,
                    'type' => 'training',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:50',
                    'price' => 35,
                    'type' => 'training',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:55',
                    'price' => 40,
                    'type' => 'training',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                

                //walking
                [
                    'duration' => '00:15',
                    'price' => 15,
                    'type' => 'walking',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:20',
                    'price' => 10,
                    'type' => 'walking',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:25',
                    'price' => 20,
                    'type' => 'walking',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:30',
                    'price' => 35,
                    'type' => 'walking',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:35',
                    'price' => 40,
                    'type' => 'walking',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:40',
                    'price' => 30,
                    'type' => 'walking',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:45',
                    'price' => 25,
                    'type' => 'walking',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:50',
                    'price' => 34,
                    'type' => 'walking',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
                [
                    'duration' => '00:55',
                    'price' => 27,
                    'type' => 'walking',
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                ],
               
            ];
            foreach ($data as $key => $value) {
                $serviceduration = [
                    'duration' => $value['duration'],
                    'price' => $value['price'],
                    'type' => $value['type'],
                    'status' => $value['status'],
                    'created_by' => $value['created_by'],
                    'updated_by' => $value['updated_by'],
                ];
                $serviceduration = ServiceDuration::create($serviceduration);
            }
        }
        // Enable foreign key checks!
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function attachFeatureImage($model, $publicPath)
    {
        if(!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('feature_image');

        return $media;
    }
}
