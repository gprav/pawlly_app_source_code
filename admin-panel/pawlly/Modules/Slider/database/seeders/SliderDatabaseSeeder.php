<?php

namespace Modules\Slider\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Slider\Models\Slider;
use Modules\Category\Models\Category;
use Illuminate\Support\Arr;

class SliderDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = \Faker\Factory::create();

        $categoryIds = Category::whereNull('parent_id')->pluck('id')->toArray();

        $sliders = [
            [
                'name' => 'Slider 1',
                'link' => 'https://example.com/slider1',
                'feature_image' => public_path('/dummy-images/sliders/vetrinary.jpg')
            ],
            [
                'name' => 'Slider 2',
                'link' => 'https://example.com/slider1',
                'feature_image' => public_path('/dummy-images/sliders/day care.png')
            ],
            [
                'name' => 'Slider 3',
                'link' => 'https://example.com/slider1',
                'feature_image' => public_path('/dummy-images/sliders/grooming.png')
            ],
            // [
            //     'link' => 4,
            //     'feature_image' => public_path('/dummy-images/sliders/feeding_and_watering.png')
            // ],
            // [
            //     'link' => 4,
            //     'feature_image' => public_path('/dummy-images/sliders/fear_anxiety_management.png')
            // ],
            // [
            //     'link' => 4,
            //     'feature_image' => public_path('/dummy-images/sliders/trick_training.png')
            // ],
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($sliders  as $key => $sliders_data) {
                $featureImage = $sliders_data['feature_image'] ?? null;
                $slidersData = Arr::except($sliders_data, [ 'feature_image']);
                $slider = Slider::create($slidersData);

                $this->attachFeatureImage($slider, $featureImage);
            }
        }

        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    }

    private function attachFeatureImage($model, $publicPath)
    {
        if(!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('feature_image');

        return $media;
    }
}
