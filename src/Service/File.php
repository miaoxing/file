<?php

namespace Miaoxing\File\Service;

use Miaoxing\Plugin\BaseService;
use Wei\Ret;
use Wei\Upload;

/**
 * 处理文件相关逻辑
 *
 * 1. 将文件保存到 Storage 中，并记录到数据库中（save，saveRemote 等）
 * 2. 存储路径和扩展名管理，适用于本地存储（Localstorage），上传（Upload 服务）和下载操作（getAllowedExts，getRoot，generatePath 等）
 *
 * @mixin \LoggerMixin
 * @mixin \AppMixin
 * @mixin \ReqMixin
 * @mixin \FsMixin
 * @mixin \IsImageMixin
 * @mixin \HttpPropMixin
 * @mixin \StorageMixin
 * @mixin \LocalStorageMixin
 * @property Upload $upload （phpstan）不用 UploadMixin 以免和 upload 方法冲突
 */
class File extends BaseService
{
    /**
     * 将本地文件上传到文件存储中，按需原来文件，并记录文件到数据库
     *
     * @param string $path
     * @param array{origName?: string, size?: int, md5?: string, path?: string} $options
     * @return Ret
     * @svc
     */
    protected function saveLocal(string $path, array $options = []): Ret
    {
        // "public/" is meaningless for remote storage
        if (!isset($options['path'])) {
            $options['path'] = $this->fs->stripPublic($path);
        }

        $attributes = $this->getAttributes($path, $options);

        $ret = $this->storage->moveLocal($path, $options);
        if ($ret->isErr()) {
            $this->logger->warning('Failed to upload file', $ret->toArray() + ['path' => $path]);
            return $ret;
        }

        $file = $this->saveModel($attributes);

        return $file->toRet();
    }

    /**
     * 将远程文件上传到文件存储中，并记录到数据库
     *
     * @param string $url
     * @param array{path?: string, ext?: string} $options
     * @return Ret
     * @svc
     */
    protected function saveRemote(string $url, array $options = []): Ret
    {
        // 1. 获取远程文件
        $http = $this->http->request([
            'url' => $url,
            'timeout' => 10000,
            'referer' => true,
            'throwException' => false,
        ]);
        if (!$http->isSuccess()) {
            return $http->toRet();
        }

        // 2. 保存到本地
        // ignore query string
        $path = parse_url($url, \PHP_URL_PATH);
        if (!isset($options['ext'])) {
            $options['ext'] = $this->fs->getExt($path);
        }

        $writePath = $this->buildPath($options);
        $this->localStorage->write($writePath, $http->getResponse());

        // 3. 调用上传
        return $this->saveLocal($writePath, [
            'origName' => basename($path),
            'size' => strlen($http->getResponse()),
        ]);
    }

    /**
     * 获取用于存储文件的根目录
     *
     * @svc
     */
    protected function getRoot(): string
    {
        return $this->upload->getDir() . '/' . $this->app->getId();
    }

    /**
     * 生成用于存储文件的路径，包括目录和名称
     *
     * @svc
     */
    protected function generatePath(?string $ext = null): string
    {
        return $this->getRoot()
            . '/' . date('Ymd')
            . '/' . date('His') . mt_rand(100000, 999999)
            . ($ext ? '.' : '') . $ext;
    }

    /**
     * 根据扩展名称，获取文件类型
     */
    protected function detectType(?string $ext = null): int
    {
        if (null === $ext) {
            return FileModel::TYPE_OTHERS;
        }
        if ($this->upload->isAllowedImageExt($ext)) {
            return FileModel::TYPE_IMAGE;
        }
        return FileModel::TYPE_OTHERS;
    }

    /**
     * 根据传入的选项生成路径
     *
     * @param array{path?: string, ext?: string} $options
     * @return string
     * @internal
     */
    protected function buildPath(array $options = []): string
    {
        $path = $this->getRoot();

        // 指定了完整的路径，例如远程文件同步到本地
        if (isset($options['path'])) {
            return $path . '/' . $options['path'];
        }

        // 生成默认路径
        return $this->generatePath($options['ext'] ?? null);
    }

    /**
     * 根据文件路径获取文件的属性
     *
     * @internal
     */
    protected function getAttributes(string $path, array $options = []): array
    {
        $ext = $this->fs->getExt($path);

        $attributes = [
            'path' => $path,
            'origName' => $this->truncate($options['origName'] ?? null, 64),
            'size' => $options['size'] ?? filesize($path),
            'md5' => $options['md5'] ?? md5_file($path),
            'ext' => $ext,
            'type' => $this->detectType($ext),
            'url' => $this->storage->getUrl($options['path'] ?? $path),
        ];

        // 计算图片宽高
        if ($this->isImage->isValid($path)) {
            $attributes['width'] = $this->isImage->getOption('width');
            $attributes['height'] = $this->isImage->getOption('height');
        }

        return $attributes;
    }

    /**
     * 保存文件记录到数据库中
     *
     * @param array $attributes
     * @return FileModel
     * @internal
     */
    protected function saveModel(array $attributes): FileModel
    {
        return FileModel::saveAttributes($attributes);
    }

    /**
     * @internal
     */
    protected function truncate(?string $str, int $length): ?string
    {
        if (mb_strlen($str) > $length) {
            return mb_substr($str, 0, $length);
        }
        return $str;
    }
}
