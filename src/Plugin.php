<?php

namespace Miaoxing\File;

class Plugin extends \Miaoxing\Plugin\BasePlugin
{
    protected $name = '文件管理';

    protected $description = '提供文件上传,下载等功能';

    /**
     * 保存,将图片地址转换为虚拟地址
     *
     * @param string|array $image
     */
    public function onPreImageSave(&$image)
    {
        $image = wei()->cdn->convertToVirtualUrl($image);
    }

    /**
     * 保存前,将数据中多个字段的图片地址转换为虚拟地址
     *
     * @param array $data
     * @param array $keys
     */
    public function onPreImageDataSave(&$data, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data[$key] = wei()->cdn->convertToVirtualUrl($data[$key]);
            }
        }
    }

    /**
     * 将图片地址转换为真实地址
     *
     * @param string|array $image
     */
    public function onPostImageLoad(&$image)
    {
        $image = wei()->cdn->convertToRealUrl($image);
    }

    /**
     * 将数据中多个字段的图片地址转换为真实地址
     *
     * @param array $data
     * @param array $keys
     */
    public function onPostImageDataLoad(&$data, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data[$key] = wei()->cdn->convertToRealUrl($data[$key]);
            }
        }
    }
}
