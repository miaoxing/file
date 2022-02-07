<?php

/**
 * @property    Miaoxing\File\Service\File $file 处理文件相关逻辑
 */
class FileMixin
{
}

/**
 * @property    Miaoxing\File\Service\FileModel $fileModel
 * @method      Miaoxing\File\Service\FileModel fileModel() 返回当前对象
 */
class FileModelMixin
{
}

/**
 * @mixin FileMixin
 * @mixin FileModelMixin
 */
class AutoCompletion
{
}

/**
 * @return AutoCompletion
 */
function wei()
{
    return new AutoCompletion();
}

/** @var Miaoxing\File\Service\File $file */
$file = wei()->file;

/** @var Miaoxing\File\Service\FileModel $file */
$file = wei()->fileModel;

/** @var Miaoxing\File\Service\FileModel|Miaoxing\File\Service\FileModel[] $files */
$files = wei()->fileModel();
