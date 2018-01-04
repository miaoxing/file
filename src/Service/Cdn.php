<?php

namespace Miaoxing\File\Service;

/**
 * @property \Miaoxing\App\Service\Logger $logger
 */
class Cdn extends \Miaoxing\Plugin\BaseService
{
    protected $exts = [
        'jpg', 'jpeg', 'gif', 'png', 'bmp',
    ];

    protected $hosts = [];

    /**
     * 如 http://dev.s1.m.com/
     *
     * @var string
     */
    protected $cdnHost;

    /**
     * 虚拟地址,用于存储在数据库中
     *
     * @var string
     */
    protected $virtualUrl = '//%host%';

    /**
     * 老的地址,用于兼容已有的数据和虚拟地址升级
     *
     * 如: http://old-cdn.example.com
     *
     * @var string
     */
    protected $oldUrl;

    /**
     * 真实的地址,如CDN地址,用于展示给用户
     *
     * 如: //image-10001577.image.myqcloud.com
     *
     * @var string
     */
    protected $realUrl;

    /**
     * 将HTML中的图片地址替换/上传为CDN地址
     *
     * @param string $html
     * @return string
     */
    public function uploadImagesFromHtml($html)
    {
        return preg_replace_callback("/<img.+?src=[\"'](.+?)[\"'].*?>/i", [$this, 'updateUrl'], $html);
    }

    /**
     * @param array $matches
     * @return string
     */
    protected function updateUrl($matches)
    {
        list($ori, $url) = $matches;
        $url = trim($url);

        if (!$url) {
            $this->logger->info('Empty url: ' . $ori);

            return $ori;
        }

        // 检查是否为图片
        $ext = wei()->file->getExt($url);
        if (!in_array($ext, $this->exts)) {
            $this->logger->info('Ignore invalid image extension', ['url' => $url]);

            return $ori;
        }

        $parts = parse_url($url);

        // 已经是CDN域名
        if (isset($parts['host']) && in_array($parts['host'], $this->hosts)) {
            return $ori;
        }

        // 本地地址,加上CDN地址
        if (!isset($parts['host'])) {
            $newUrl = $this->cdnHost . ltrim($url, '/');
            $this->logger->info('Replace content image', [
                'from' => $url,
                'to' => $newUrl,
            ]);

            return str_replace($url, $newUrl, $ori);
        }

        // 远程地址,下载到CDN
        $newUrl = $this->upload($url);
        if (!$newUrl) {
            return $ori;
        }

        // 组装成新地址
        $this->logger->info('Replace content image', [
            'from' => $url,
            'to' => $newUrl,
        ]);

        return str_replace($url, $newUrl, $ori);
    }

    /**
     * 下载文件到本地
     *
     * @param string $remoteFile
     * @return bool|string
     */
    public function download($remoteFile)
    {
        // 获取文件内容
        $http = wei()->http([
            'url' => $remoteFile,
            'timeout' => 10000,
            'referer' => true,
            'throwException' => false,
        ]);
        if (!$http->isSuccess()) {
            return false;
        }

        // 保存到本地
        $dir = wei()->upload->getDir() . '/' . $this->app->getId() . '/' . date('Ymd');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $file = $dir . time() . rand(1, 10000) . '.' . wei()->file->getExt($remoteFile);
        file_put_contents($file, $http->getResponse());

        return $file;
    }

    /**
     * 上传图片到CDN
     *
     * @param string $remoteFile
     * @return bool|string
     * @todo 独立为服务
     */
    public function upload($remoteFile)
    {
        // 调用ueditor接口下载图片,确保图片只在一台服务器中
        $app = wei()->app->getNamespace();
        $url = wei()->ueditor->getOption('imageUrl') . $this->url->append('/ueditor/get-remote-image', ['app' => $app]);
        $http = wei()->http([
            'url' => $url,
            'dataType' => 'json',
            'throwException' => false,
            'method' => 'post',
            'data' => [
                'upfile' => $remoteFile,
            ],
        ]);

        if (!$http->isSuccess()) {
            return false;
        }

        if (!isset($http['urls'][0])) {
            $this->logger->alert('下载远程图片失败', [
                'url' => $remoteFile,
                'response' => $http->getResponse(),
            ]);

            return false;
        }

        return $http['urls'][0];
    }

    /**
     * 更新内容中的地址为虚拟地址,用于存储
     *
     * @param string|array $content
     * @return string|array
     */
    public function convertToVirtualUrl($content)
    {
        // 如果替换的key为空,strtr会返回false
        if (!$this->realUrl) {
            return $content;
        }
        if (is_array($content)) {
            return array_map([$this, __METHOD__], $content);
        }

        return strtr($content, [$this->realUrl => $this->virtualUrl]);
    }

    /**
     * 更新内容中的地址为真实地址(如CDN地址),用于展示
     *
     * @param string|array $content
     * @return string|array
     */
    public function convertToRealUrl($content)
    {
        if (is_array($content)) {
            return array_map([$this, __METHOD__], $content);
        }

        // 构造替换的数据,允许没有旧地址的情况
        $replaces = [$this->virtualUrl => $this->realUrl];
        if ($this->oldUrl) {
            $replaces[$this->oldUrl] = $this->realUrl;
        }

        return strtr($content, $replaces);
    }
}
