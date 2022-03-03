<?php

namespace Miaoxing\File\Service;

use Miaoxing\File\Metadata\FileTrait;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\SnowflakeTrait;

class FileModel extends BaseModel
{
    use FileTrait;
    use HasAppIdTrait;
    use ModelTrait;
    use SnowflakeTrait;

    public const TYPE_IMAGE = 1;

    public const TYPE_DOC = 2;

    public const TYPE_AUDIO = 3;

    public const TYPE_VIDEO = 4;

    public const TYPE_OTHERS = 99;
}
