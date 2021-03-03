<?php

namespace Database\Factories;

use App\Models\CovidMaster;
use Illuminate\Database\Eloquent\Factories\Factory;

class CovidMasterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CovidMaster::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => 'total',
            'gubun' => '합계',
            'gubun_en' => 'Total',
        ];
    }
}
