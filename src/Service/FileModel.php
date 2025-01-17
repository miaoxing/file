<?php

namespace Miaoxing\File\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\SnowflakeTrait;

/**
 * @property string|null $id
 * @property string $appId
 * @property string $origName
 * @property string $path
 * @property int $type 文件类型,1是图片,2是文档,3是音频,4是视频
 * @property string $ext 扩展名
 * @property int $size
 * @property int $width
 * @property int $height
 * @property string $md5
 * @property string $url
 * @property string|null $origCreatedAt
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property string $createdBy
 * @property string $updatedBy
 */
class FileModel extends BaseModel
{
    use HasAppIdTrait;
    use ModelTrait;
    use SnowflakeTrait;

    public const TYPE_IMAGE = 1;

    public const TYPE_DOC = 2;

    public const TYPE_AUDIO = 3;

    public const TYPE_VIDEO = 4;

    public const TYPE_OTHERS = 99;
}
