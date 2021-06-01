<?php

namespace Database\Factories;

use App\Models\Weathers;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * Class WeathersFactory
 * @package Database\Factories
 */
class WeathersFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Weathers::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        return [
            'area_code_id' => '1',
            'fcstDate' => Carbon::now()->format('Ymd'),
            'fcstTime' => Carbon::now()->format('H00'),
            'T1H' => '13',
            'RN1' => '0',
            'SKY' => '1',
            'UUU' => '-2.9',
            'VVV' => '1.8',
            'REH' => '45',
            'PTY' => '0',
            'LGT' => '0',
            'VEC' => '121',
            'WSD' => '3'
        ];
    }
}
