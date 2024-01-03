<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\MediaFiles
 *
 * @property int $id
 * @property string $dest_path 저장 디렉토리.
 * @property string $file_name 파일명.
 * @property string $thumb_name 썸네일 파일명.
 * @property string $original_name 원본 파일명.
 * @property string $height
 * @property string $width
 * @property string $file_type 원본 파일 타입.
 * @property int $file_size 파일 용량.
 * @property string $file_extension 파일 확장자.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|MediaFiles newModelQuery()
 * @method static Builder|MediaFiles newQuery()
 * @method static Builder|MediaFiles query()
 * @method static Builder|MediaFiles whereCreatedAt($value)
 * @method static Builder|MediaFiles whereDeletedAt($value)
 * @method static Builder|MediaFiles whereDestPath($value)
 * @method static Builder|MediaFiles whereFileExtension($value)
 * @method static Builder|MediaFiles whereFileName($value)
 * @method static Builder|MediaFiles whereFileSize($value)
 * @method static Builder|MediaFiles whereFileType($value)
 * @method static Builder|MediaFiles whereHeight($value)
 * @method static Builder|MediaFiles whereId($value)
 * @method static Builder|MediaFiles whereOriginalName($value)
 * @method static Builder|MediaFiles whereThumbName($value)
 * @method static Builder|MediaFiles whereUpdatedAt($value)
 * @method static Builder|MediaFiles whereWidth($value)
 * @mixin Eloquent
 */
class MediaFiles extends Model
{
	use HasFactory;

	/**
	 * @var string[]
	 */
	protected $fillable = [
		'dest_path',
		'file_name',
		'thumb_name',
		'original_name',
		'height',
		'width',
		'file_type',
		'file_size',
		'file_extension',
	];
}
