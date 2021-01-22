<?php

namespace Miaoxing\File\Service;

use Miaoxing\File\Metadata\FileTrait;
use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\Model;

class FileModel extends BaseModel
{
    use ModelTrait;
    use FileTrait;
    use HasAppIdTrait;
}
