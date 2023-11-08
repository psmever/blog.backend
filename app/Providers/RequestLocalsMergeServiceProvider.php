<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class RequestLocalsMergeServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		Request::macro('LocalsMergeMacro', function (string $mergeName, string $mergeValue) {
			$this->merge(["locals" => [$mergeName => $mergeValue]]);
		});
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		//
	}
}
