<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Codes
 *
 * @property int $id
 * @property string $group_id
 * @property string|null $code_id
 * @property string|null $group_name
 * @property string|null $code_name
 * @property string $active 사용 상태(사용중, 비사용)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Codes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Codes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Codes query()
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereCodeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Codes whereUpdatedAt($value)
 */
	class Codes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $user_uuid 사용자 uuid
 * @property string $user_type 사용자 타입
 * @property string $user_level 사용자 레벨
 * @property string $name
 * @property string $nickname 사용자 닉네임
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string $active 사용자 상태
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\Codes|null $userLevel
 * @property-read \App\Models\Codes|null $userType
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserUuid($value)
 */
	class User extends \Eloquent {}
}

