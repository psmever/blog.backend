<?php

namespace Database\Factories;

use App\Models\VilageFcstinfoMaster;
use Illuminate\Database\Eloquent\Factories\Factory;

class VilageFcstinfoMasterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VilageFcstinfoMaster::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'version' => Carbon::now()->format('Ymd')
        ];
    }
}
