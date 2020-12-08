<?php

namespace Miaoxing\File\Metadata;

use Miaoxing\Plugin\Model\ModelTrait;

/**
 * FileTrait
 *
 * @property int $id
 * @property int $appId
 * @property string $origName
 * @property string $path
 * @property int $type 文件类型,1是图片,2是文件,3是音频,4是视频
 * @property string $ext 扩展名
 * @property int $size
 * @property int $width
 * @property int $height
 * @property string $md5
 * @property string $url
 * @property string|null $origCreatedAt
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * @internal will change in the future
 */
trait FileTrait
{
    use ModelTrait;

    /**
     * @var array
     * @see CastTrait::$casts
     */
    protected $casts = [
        'id' => 'int',
        'appId' => 'int',
        'origName' => 'string',
        'path' => 'string',
        'type' => 'int',
        'ext' => 'string',
        'size' => 'int',
        'width' => 'int',
        'height' => 'int',
        'md5' => 'string',
        'url' => 'string',
        'origCreatedAt' => 'datetime',
        'createdAt' => 'datetime',
        'updatedAt' => 'datetime',
        'createdBy' => 'int',
        'updatedBy' => 'int',
    ];
}
