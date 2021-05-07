<?php

namespace Miaoxing\File\Service;

use Miaoxing\File\Metadata\FileTrait;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;

class FileModel extends BaseModel
{
    use FileTrait;
    use HasAppIdTrait;
    use ModelTrait;
}
