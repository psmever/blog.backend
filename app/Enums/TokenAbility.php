<?php

namespace App\Enums;

enum TokenAbility: string
{
	case ACCESS_API = 'access-api';
	case REFRESH_TOKEN = 'refresh-token';
}
