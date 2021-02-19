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
 * App\Models\MediaFiles
 *
 * @property int $id
 * @property string $dest_path 저장 디렉토리.
 * @property string $file_name 파일명.
 * @property string $original_name 원본 파일명.
 * @property string $file_type 원본 파일 타입.
 * @property int $file_size 파일 용량.
 * @property string $file_extension 파일 확장자.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles whereDestPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles whereFileExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFiles whereUpdatedAt($value)
 */
	class MediaFiles extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Posts
 *
 * @property int $id
 * @property int $user_id 사용자 id
 * @property string $post_uuid
 * @property string $title
 * @property string $slug_title
 * @property string $contents_html
 * @property string $contents_text
 * @property string $markdown 마크다운 유무.
 * @property string $post_publish 게시 유무.
 * @property string $post_active 글 공개 여부.
 * @property int $view_count 뷰 카운트.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PostsTags[] $tag
 * @property-read int|null $tag_count
 * @property-read \App\Models\PostsThumbs|null $thumb
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Posts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Posts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Posts query()
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereContentsHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereContentsText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereMarkdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts wherePostActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts wherePostPublish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts wherePostUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereSlugTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Posts whereViewCount($value)
 */
	class Posts extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PostsTags
 *
 * @property int $id
 * @property int $post_id post id.
 * @property string|null $tag_id 테그 id.
 * @property string|null $tag_text 테그 내용.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Posts|null $posts
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereTagText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsTags whereUpdatedAt($value)
 */
	class PostsTags extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PostsThumbs
 *
 * @property int $id
 * @property int $post_id post id.
 * @property int|null $media_file_id media file table id.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MediaFiles|null $file
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs whereMediaFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostsThumbs whereUpdatedAt($value)
 */
	class PostsThumbs extends \Eloquent {}
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

namespace App\Models{
/**
 * App\Models\VilageFcstinfo
 *
 * @property int $id
 * @property int $version_id 버전 id.
 * @property string $gubun 구분.
 * @property string $area_code 행정구역코드.
 * @property string $step1 1단계.
 * @property string $step2 2단계.
 * @property string $step3 3단계.
 * @property string $grid_x 격자 X.
 * @property string $grid_y 격자 Y.
 * @property string $longitude_hour 경도(시).
 * @property string $longitude_minute 경도(분).
 * @property string $longitude_second 경도(초).
 * @property string $latitude_hour 위도(시).
 * @property string $latitude_minute 위도(분).
 * @property string $latitude_second 위도(초).
 * @property string $longitude 경도(초/100).
 * @property string $latitude 위도(초/100).
 * @property string $update_time 위치업데이트.
 * @property string $active 사용 유무.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereGridX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereGridY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereGubun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLatitudeHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLatitudeMinute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLatitudeSecond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLongitudeHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLongitudeMinute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereLongitudeSecond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereStep1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereStep2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereStep3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfo whereVersionId($value)
 */
	class VilageFcstinfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\VilageFcstinfoMaster
 *
 * @property int $id
 * @property string $version
 * @property string $active 사용 유무.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\VilageFcstinfo|null $vilage_fcstinfo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\VilageFcstinfo[] $vilage_fcstinfos
 * @property-read int|null $vilage_fcstinfos_count
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster query()
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VilageFcstinfoMaster whereVersion($value)
 */
	class VilageFcstinfoMaster extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Weathers
 *
 * @property int $id
 * @property int $area_code_id 행정구역코드 ID.
 * @property string $fcstDate 예측일자.
 * @property string $fcstTime 예측시간.
 * @property string $T1H 기온.
 * @property string $RN1 1시간 강수량.
 * @property string $SKY 하늘상태(맑음(1), 구름많음(3), 흐림(4))
 * @property string $UUU 동서바람성분.
 * @property string $VVV 남북바람성분.
 * @property string $REH 습도.
 * @property string $PTY 강수형태(없음(0), 비(1), 비/눈(2), 눈(3), 소나기(4), 빗방울(5), 빗방울/눈날림(6), 눈날림(7))
 * @property string $LGT 낙뢰.
 * @property string $VEC 풍향.
 * @property string $WSD 풍속.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers query()
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereAreaCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereFcstDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereFcstTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereLGT($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers wherePTY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereREH($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereRN1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereSKY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereT1H($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereUUU($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereVEC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereVVV($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Weathers whereWSD($value)
 */
	class Weathers extends \Eloquent {}
}

