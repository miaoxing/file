<?php

namespace Miaoxing\File\Service;

use Miaoxing\File\Metadata\FileTrait;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Service\Model;

class FileModel extends Model
{
    use FileTrait;
    use HasAppIdTrait;
}
