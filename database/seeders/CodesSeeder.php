<?php

namespace Database\Seeders;

use App\Models\Codes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CodesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0');
		Codes::truncate();

		foreach (config('appData.codes') as $codes) {
			$groupCode = $codes['group'];
			$groupName = $codes['name'];

			if (!Codes::where(['group' => $groupCode, 'code' => null])->exists()) {
				Codes::create([
					'group' => $groupCode,
					'group_name' => $groupName
				]);
			}

			foreach ($codes['list'] as $codeItem) {
				$code = $groupCode . $codeItem['code'];
				$name = $codeItem['name'];

				Codes::create([
					'group' => $groupCode,
					'code' => $code,
					'code_name' => $name
				]);
			}
		}

		DB::statement('SET FOREIGN_KEY_CHECKS=1');
	}
}
