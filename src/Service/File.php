<?php

namespace Miaoxing\File\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Service\Ret;
use Wei\Upload;

/**
 * @mixin \LoggerMixin
 * @mixin \AppMixin
 * @mixin \ReqMixin
 * @property Upload $upload （phpstan）不用 UploadMixin 以免和 upload 方法冲突
 */
class File extends BaseService
{
    public const TYPE_IMAGE = 1;

    public const TYPE_DOC = 2;

    public const TYPE_VOICE = 3;

    public const TYPE_VIDEO = 4;

    /**
     * 文件上传服务
     *
     * @var string
     */
    protected $driver = 'file';

    /**
     * 图片类型的扩展名
     *
     * @var array
     */
    protected $imageExts = [
        'jpg',
        'jpeg',
        'png',
        'bmp',
        'gif',
    ];

    /**
     * 特殊需转换的语音类型的扩展名
     *
     * @var array
     */
    protected $voiceExts = [
        'amr',
    ];

    /**
     * 保存图片记录到数据库
     *
     * @param Ret $ret
     * @param array $extraData
     * @return FileModel
     */
    protected function saveRet(Ret $ret, array $extraData = [])
    {
        if (isset($ret['pathName'])) {
            $path = $ret['pathName'];
        } else {
            $path = parse_url($ret['url'], \PHP_URL_PATH);
            $path = ltrim($path, '/');
        }

        // 自动计算宽高
        $isImage = wei()->isImage;
        if (!isset($ret['width']) && $isImage($path)) {
            $ret['width'] = $isImage->getOption('width');
            $ret['height'] = $isImage->getOption('height');
        }

        // 合并检查文件操作
        if (!isset($ret['size']) || !isset($ret['md5'])) {
            $isFile = is_file($path);
        } else {
            $isFile = false;
        }

        // 自动计算大小
        if (!isset($ret['size']) && $isFile) {
            $ret['size'] = filesize($path);
        }

        // 自动计算md5
        if (!isset($ret['md5']) && $isFile) {
            $ret['md5'] = md5_file($path);
        }

        return FileModel::save([
                'origName' => (string) $ret['origName'],
                'path' => $path,
                'url' => $ret['url'],
                'ext' => $this->getExt($ret['url']),
                'type' => static::TYPE_IMAGE, // 暂时都是图片
                'size' => (int) $ret['size'],
                'width' => (int) $ret['width'],
                'height' => (int) $ret['height'],
                'md5' => (string) $ret['md5'],
            ] + $extraData);
    }

    /**
     * 获取文件服务并上传
     *
     * @param string $file
     * @param string $ext 保存的文件后缀
     * @param string|true $customName 自定义的完整文件名称
     * @param array $extraData
     * @return Ret
     * @svc
     */
    protected function upload($file, $ext = '', $customName = '', $extraData = [])
    {
        // 1. 获取存储服务
        /** @var File $service */
        $service = $this->wei->get($this->driver);

        if (true === $customName) {
            $customName = $this->generateName() . '.' . ($ext ?: $this->getExt($file));
        }

        // 2. 写入到存储服务中
        $ret = $service->write($file, $ext, $customName);
        if ($ret->isSuc()) {
            $ret['data'] = $this->saveRet($ret, $extraData);
        } else {
            $this->logger->warning('文件上传失败', $ret->toArray() + ['file' => $file]);
        }

        return $ret;
    }

    /**
     * 上传文件接口
     *
     * 返回结果
     * [
     *   'code' => 1,
     *   'message' => '上传成功'
     *   'url' => 'http://xxx',
     *   'originalName' => 'xxx.jpg',
     *   'size' => 100, // 可选
     *   'width' => 100, // 可选
     *   'height' => 100, // 可选
     *   'md5' => 'abc' // 可选
     * ]
     *
     * @param string $file
     * @param string $ext 保存的文件后缀
     * @param string $customName 自定义的完整文件名称
     * @return Ret
     */
    public function write($file, $ext = '', $customName = '')
    {
        $localFile = $this->downloadIfRemote($file, $ext, '', $customName);
        if (!$localFile) {
            return err(['文件%s下载失败', $file]);
        }

        $localFile = $this->transform($file, $ext, $localFile);
        $url = $this->getFileUrl($localFile);
        $url = $this->req->getUrlFor($url);

        return suc([
            'message' => '上传成功',
            'url' => $url,
            'pathName' => $file,
            'origName' => $this->getFileName($file),
        ]);
    }

    /**
     *   文件格式转换
     * @param string $file 远程文件url
     * @param string $ext 文件扩展名
     * @param string $localFile 本地服务器文件路径
     * @return mixed
     */
    public function transform($file, $ext, $localFile)
    {
        // 如果是语音文件，则进行文件转换
        if ($this->isVoiceExt($this->getExt($file, $ext))) {
            $amr = $localFile;
            $mp3 = str_replace('.' . $ext, '.mp3', $localFile);

            if (!file_exists($mp3)) {
                $command = "ffmpeg -i $amr $mp3";
                system($command, $error);
            }
            $localFile = $mp3;
        }

        return $localFile;
    }

    /**
     * 如果是远程文件,下载到本地
     *
     * @param string $file
     * @param string $ext 保存的文件后缀
     * @param string $path 指定保存的文件路径
     * @param string $customName 自定义的完整文件名称
     * @return bool|string
     */
    public function downloadIfRemote($file, $ext = '', $path = '', $customName = '')
    {
        $host = parse_url($file, \PHP_URL_HOST);
        if ($host) {
            $file = $this->download($file, $ext, $path, $customName);
        }

        return $file;
    }

    /**
     * 按需下载文件到本地
     *
     * @param string $file
     * @param string $ext 保存的文件后缀
     * @return bool|string
     */
    public function downloadOnDemand($file, $ext = '')
    {
        // 1. 如果是/开头,认为是当前目录中的素材
        if ('/' == $file[0]) {
            $file = ltrim($file, '/');

            return $file;
        }

        // 2. 下载文件到本地固定位置
        return $this->downloadIfRemote($file, $ext, md5($file));
    }

    /**
     * 用于删除从远程下载过来的临时文件
     *
     * @param string $localFile
     * @param string $file
     */
    protected function removeIfRemote($localFile, $file)
    {
        if ($localFile != $file) {
            unlink($localFile);
        }
    }

    /**
     * 下载远程文件
     *
     * @param string $remoteFile
     * @param string $ext 保存的文件后缀
     * @param string $path 指定保存的文件路径
     * @param string $customName 自定义的完整文件名称,包含路径和文件名
     * @return bool|string
     */
    public function download($remoteFile, $ext = '', $path = '', $customName = '')
    {
        // 1. 生成文件目录,名称等
        if (!$ext) {
            $ext = $this->getExt($remoteFile);
        }

        // TODO 简化合并类似逻辑
        if ($customName) {
            $dir = dirname($customName);
            $file = $customName;
        } elseif ($path) {
            // 如果指定了路径且文件存在,返回已有的地址
            $dir = dirname($this->getUploadDir()) . '/' . substr($path, 0, 2);
            $file = $dir . '/' . $path . '.' . $ext;
            if (is_file($file)) {
                return $file;
            }
        } else {
            $dir = $this->getUploadDir();
            $file = $dir . '/' . $this->getUploadName() . '.' . $ext;
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // 2. 远程获取文件
        $http = wei()->http([
            'url' => $remoteFile,
            'timeout' => 10000,
            'referer' => true,
            'throwException' => false,
        ]);
        if (!$http->isSuccess()) {
            return false;
        }

        // 3. 保存文件并返回
        file_put_contents($file, $http->getResponse());

        return $file;
    }

    public function generateName()
    {
        return $this->getUploadDir() . '/' . $this->getUploadName();
    }

    /**
     * 获取文件扩展名
     *
     * @param string $file
     * @param string $default
     * @return string
     */
    public function getExt($file, $default = 'jpg')
    {
        // 如果是远程文件,可能带有请求参数?xxx=xxx
        $file = parse_url($file, \PHP_URL_PATH);
        $pos = strrpos($file, '.');
        if (false !== $pos) {
            return strtolower(substr($file, $pos + 1));
        } else {
            return $default;
        }
    }

    /**
     * 获取文件名称,不包含请求参数
     *
     * @param string $file
     * @return string
     */
    public function getFileName($file)
    {
        $parts = explode('/', parse_url($file, \PHP_URL_PATH));
        return end($parts);
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * 检查扩展名是否为图片类型
     *
     * @param string $ext
     * @return bool
     */
    public function isImageExt($ext)
    {
        return in_array($ext, $this->imageExts, true);
    }

    /**
     * 检查扩展名是否为需转换语音类型
     *
     * @param string $ext
     * @return bool
     */
    public function isVoiceExt($ext)
    {
        return in_array($ext, $this->voiceExts, true);
    }

    /**
     * 获取需转换语音类型的扩展名
     *
     * @return array
     */
    protected function getVoiceExts()
    {
        return $this->voiceExts;
    }

    /**
     * 获取可通过URL访问的地址
     *
     * @param string $file
     * @return string
     */
    public function getFileUrl(string $file)
    {
        return '/' . substr($file, strlen('public/'));
    }

    /**
     * 获取所有允许上传的文件扩展名
     *
     * @return array
     * @svc
     */
    protected function getAllExts()
    {
        return array_merge(
            $this->getImageExts(),
            $this->getVoiceExts()
        );
    }

    /**
     * 获取图片的扩展名
     *
     * @return array
     * @svc
     */
    protected function getImageExts()
    {
        return $this->imageExts;
    }

    /**
     * 获取用于存储文件的目录
     *
     * @return string
     * @throws \Exception
     * @svc
     */
    protected function getUploadDir()
    {
        $dir = $this->upload->getDir() . '/' . $this->app->getId() . '/' . date('ymd');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
     * 生成用于存储的文件名称(不带扩展名)
     *
     * @return string
     * @svc
     */
    protected function getUploadName()
    {
        return date('His') . mt_rand(100000, 999999);
    }
}
