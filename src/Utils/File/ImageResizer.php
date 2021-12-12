<?php

namespace App\Utils\File;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

Class ImageResizer
{
    /**
     * @var Image
     */
    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * @param string $originalFileFolder
     * @param string $originalFilename
     * @param array $targetParams
     * @return string
     */
    public function resizeImageAndSave(string $originalFileFolder, string  $originalFilename, array $targetParams) : string
    {
        $originalFilePath = $originalFileFolder.'/'.$originalFilename;
        list($imageWidth, $imageHeight) = getimagesize($originalFilePath);

        $ratio = $imageWidth / $imageHeight;
        $targetWidth = $targetParams['width'];
        $targetHeight = $targetParams['height'];

        if($targetHeight){ //указан и не равен null
            if($targetWidth / $targetHeight > $ratio){
                $targetWidth = $targetHeight * $ratio;
            }else{
                $targetHeight = $targetWidth / $ratio;
            }
        }else{
            $targetHeight = $targetWidth / $ratio;
        }

        $targetFolder = $targetParams['newFolder'];
        $targetFileName = $targetParams['newFilename'];

        $targetFilePath = sprintf('%s/%s',$targetFolder, $targetFileName);

        $imagineFile = $this->imagine->open($originalFilePath);
        $imagineFile
            ->resize(new Box($targetWidth, $targetHeight))
            ->save($targetFilePath);

        return $targetFileName;
    }
}