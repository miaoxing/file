<?php

namespace Miaoxing\File\Metadata;

/**
 * FileTrait
 *
 * @property int $id
 * @property int $appId
 * @property string $origName
 * @property string $path
 * @property bool $type 文件类型,1是图片,2是文件,3是音频,4是视频
 * @property string $ext 扩展名
 * @property int $size
 * @property int $width
 * @property int $height
 * @property string $md5
 * @property string $url
 * @property string $origCreatedAt
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * @internal will change in the future
 */
trait FileTrait
{
    /**
     * @var array
     * @see CastTrait::$casts
     */
    protected $casts = [
        'id' => 'int',
        'app_id' => 'int',
        'orig_name' => 'string',
        'path' => 'string',
        'type' => 'bool',
        'ext' => 'string',
        'size' => 'int',
        'width' => 'int',
        'height' => 'int',
        'md5' => 'string',
        'url' => 'string',
        'orig_created_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'int',
        'updated_by' => 'int',
    ];
}
