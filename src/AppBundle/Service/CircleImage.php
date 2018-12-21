<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 10/14/16
 * Time: 3:04 PM
 */

namespace AppBundle\Service;


class CircleImage
{
    public $img;

    public $imgName;

    public $transparent;

    public $width;

    public $height;

    public function __construct()
    {

    }

    public function initiate($img=null){
        if (!empty($img)) {
            $this->img = imagecreatefrompng($img);
            $this->imgName = $img;
            $this->width = imagesx($this->img);
            $this->height = imagesy($this->img);
            $this->setTransparentColour();
        }
    }
    public function create($width, $height, $transparent)
    {
        $this->img = imagecreatetruecolor($width, $height);
        $this->width = $width;
        $this->height = $height;

        $this->setTransparentColour();

        if (true === $transparent) {
            imagefill($this->img, 0, 0, $this->transparent);
        }
    }

    public function setTransparentColour($red = 255, $green = 0, $blue = 255)
    {
        $this->transparent = imagecolorallocate($this->img, $red, $green, $blue);
        imagecolortransparent($this->img, $this->transparent);
    }

    public function circleCrop()
    {
        $mask = imagecreatetruecolor($this->width, $this->height);
        $black = imagecolorallocate($mask, 0, 0, 0);
        $magenta = imagecolorallocate($mask, 255, 0, 255);

        imagefill($mask, 0, 0, $magenta);

        imagefilledellipse(
            $mask,
            ($this->width / 2),
            ($this->height / 2),
            $this->width,
            $this->height,
            $black
        );

        imagecolortransparent($mask, $black);

        imagecopymerge($this->img, $mask, 0, 0, 0, 0, $this->width, $this->height, 100);

        imagedestroy($mask);
    }

    public function merge(Img $in, $dst_x = 0, $dst_y = 0)
    {
        imagecopymerge(
            $this->img,
            $in->img,
            $dst_x,
            $dst_y,
            0,
            0,
            $in->width,
            $in->height,
            100
        );
    }

    public function saveImage()
    {
        if (file_exists($this->imgName)) {
            unlink($this->imgName);
        }
        if(imagepng($this->img,$this->imgName)){
            return $this->imgName;
        };

    }


    // New Image  Resize function
    public function resize($imgPath, $newWidth, $newHeight)
    {

        $info = getimagesize($imgPath);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
//                $new_image_ext = 'jpg';
                break;

            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
//                $new_image_ext = 'png';
                break;

            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
//                $new_image_ext = 'gif';
                break;

            default:
                throw new Exception('Unknown image type.');
        }

        $newFileName = substr($imgPath, 0, count($imgPath) - 5) . rand(1000, 9999999999) . ".png";
        $img = $image_create_func($imgPath);
        list($width, $height) = getimagesize($imgPath);

        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if (imagepng($tmp, $newFileName)) {
            return $newFileName;
        }
    }


} 