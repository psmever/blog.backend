<?php

namespace Database\Factories;

use App\Models\CovidState;
use Illuminate\Database\Eloquent\Factories\Factory;

class CovidStateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CovidState::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'gubun_id' => '1',
            'seq' => '7836',
            'createdt' => '2021-02-24 09:36:29.051',
            'deathcnt' => '1576',
            'incdec' => '440',
            'isolclearcnt' => '79050',
            'qurrate' => '169.96',
            'stdday' => '2021년 02월 24일 00시',
            'updatedt' => 'null',
            'defcnt' => '88120',
            'isolingcnt' => '7494',
            'overflowcnt' => '23',
            'localocccnt' => '417'

        ];
    }
}
