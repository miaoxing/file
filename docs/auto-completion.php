<?php

/**
 * @property    Miaoxing\File\Service\Cdn $cdn
 */
class CdnMixin {
}

/**
 * @property    Miaoxing\File\Service\File $file
 */
class FileMixin {
}

/**
 * @property    Miaoxing\File\Service\FileModel $fileModel
 * @method      Miaoxing\File\Service\FileModel|Miaoxing\File\Service\FileModel[] fileModel($table = null)
 */
class FileModelMixin {
}

/**
 * @mixin CdnMixin
 * @mixin FileMixin
 * @mixin FileModelMixin
 */
class AutoCompletion {
}

/**
 * @return AutoCompletion
 */
function wei()
{
    return new AutoCompletion;
}

/** @var Miaoxing\File\Service\Cdn $cdn */
$cdn = wei()->cdn;

/** @var Miaoxing\File\Service\File $file */
$file = wei()->file;

/** @var Miaoxing\File\Service\FileModel $fileModel */
$file = wei()->fileModel();

/** @var Miaoxing\File\Service\FileModel|Miaoxing\File\Service\FileModel[] $fileModels */
$files = wei()->fileModel();
