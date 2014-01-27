<?php
/**
 * User: dongww
 * Date: 14-1-16
 * Time: 上午9:28
 */

namespace Data;


class Image
{
    /**
     * 上传一张图片
     *
     * @param $file
     * @return string
     */
    public function uploadFile($file)
    {
        $toPath = __DIR__ . '/../../web/upload/';
        $filename = uniqid() . '.' . $file->guessClientExtension();
        $file->move($toPath, $filename);

        return $filename;
    }

    /**
     * 上传多张图片
     *
     * @param $files
     * @return array
     */
    public function uploadFiles($files)
    {
        $fileNames = array();
        foreach ($files as $file) {
            if ($file) {
                $fileNames[] = $this->uploadFile($file);
            } else {
                continue;
            }

        }
        //文件名数组
        return $fileNames;
    }

    /**
     * 获取图片的文件路径
     *
     * @param $fileName
     * @return string
     */
    public function getPath($fileName)
    {
        return realpath(__DIR__ . '/../../web/upload/' . $fileName);
    }

    /**
     * 获取图片链接
     *
     * @param $fileName
     * @param string $pre
     * @return string
     */
    public function getUrl($fileName, $pre = '')
    {
        return '/upload/' . $pre . $fileName;
    }
} 