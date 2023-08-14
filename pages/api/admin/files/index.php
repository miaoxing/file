<?php

use Miaoxing\File\Service\File;
use Miaoxing\Plugin\BasePage;
use Miaoxing\Plugin\Service\Upload;
use Wei\Req;

return new class () extends BasePage {
    public function post(Req $req)
    {
        $ret = Upload::save([
            'exts' => 'image' === $req['type'] ? Upload::getAllowedImageExts() : Upload::getAllowedExts(),
            'path' => File::generatePath(),
        ]);
        if ($ret->isErr()) {
            return $ret;
        }

        return File::saveLocal($ret['file'], [
            'origName' => $ret['name'],
            'size' => $ret['size'],
        ]);
    }
};
