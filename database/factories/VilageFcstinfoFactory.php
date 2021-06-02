<?php

namespace Database\Factories;

use App\Models\VilageFcstinfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class VilageFcstinfoFactory
 * @package Database\Factories
 */
class VilageFcstinfoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VilageFcstinfo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        return [
            'version_id' => '1',
            'gubun' => 'kor',
            'area_code' => '1153079000',
            'step1' => '서울특별시',
            'step2' => '구로구',
            'step3' => '수궁동',
            'grid_x' => '57',
            'grid_y' => '125',
            'longitude_hour' => '126',
            'longitude_minute' => '50',
            'longitude_second' => '0.84',
            'latitude_hour' => '37',
            'latitude_minute' => '29',
            'latitude_second' => '27.98',
            'longitude' => '126.83356666666666',
            'latitude' => '37.491105555555556',
            'update_time' => '',
        ];
    }
}
