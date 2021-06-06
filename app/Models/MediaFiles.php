<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @mixin \Eloquent
 */
class MediaFiles extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'dest_path',
        'file_name',
        'original_name',
        'file_type',
        'file_size',
        'file_extension'
    ];
}
