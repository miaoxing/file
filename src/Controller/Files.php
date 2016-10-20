<?php

namespace Miaoxing\File\Controller;

use Miaoxing\File\Service\File;

class Files extends \miaoxing\plugin\BaseController
{
    public function indexAction($req)
    {
        $files = wei()->file()->curApp()->andWhere(['type' => File::TYPE_FILE]);

        return get_defined_vars();
    }
}
