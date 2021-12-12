<?php

namespace App\Utils\Manager;

use App\Utils\File\ImageResizer;
use App\Utils\Filesystem\FilesystemWorker;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ProductImage;

class ProductImageManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FilesystemWorker
     */
    private $filesystemWorker;

    /**
     * @var ImageResizer
     */
    private $imageResizer;

    /**
     * @var string
     */
    private $uploadsTempDir;


    public function __construct(EntityManagerInterface $entityManager, FilesystemWorker $filesystemWorker, ImageResizer $imageResizer, string $uploadsTempDir)
    {
        $this->entityManager = $entityManager;
        $this->filesystemWorker = $filesystemWorker;
        $this->imageResizer = $imageResizer;
        $this->uploadsTempDir = $uploadsTempDir;
    }

    /**
     * @param string $productDir
     * @param string|null $tempImageFilename
     * @return ProductImage|null
     */
    public function saveImageForProduct(string $productDir, string $tempImageFilename = null)
    {
        if(!$tempImageFilename){
            return null;
        }
        $this->filesystemWorker->createFolderIfItNotExist($productDir);
        $filenameId = uniqid();

        //small
        $imageSmallParams = [
            'width' => 60,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'small')
        ];
        $imageSmall = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir,$tempImageFilename,$imageSmallParams);

        //middle
        $imageMiddleParams = [
            'width' => 430,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'middle')
        ];
        $imageMiddle = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir,$tempImageFilename,$imageMiddleParams);


        //big
        $imageBigParams = [
            'width' => 800,
            'height' => null,
            'newFolder' => $productDir,
            'newFilename' => sprintf('%s_%s.jpg', $filenameId, 'big')
        ];
        $imageBig = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir,$tempImageFilename,$imageBigParams);


        $productImage = new ProductImage();
        $productImage->setFilenameSmall($imageSmall);
        $productImage->setFilenameMiddle($imageMiddle);
        $productImage->setFilenameBig($imageBig);

        return $productImage;
    }

    /**
     * @param ProductImage $productImage
     * @param string $productDir
     */
    public function removeImageFromProduct(ProductImage $productImage, string $productDir){
        $smallFilePath = $productDir.'/'.$productImage->getFilenameSmall();
        $this->filesystemWorker->remove($smallFilePath);

        $middleFilePath = $productDir.'/'.$productImage->getFilenameMiddle();
        $this->filesystemWorker->remove($middleFilePath);

        $bigFilePath = $productDir.'/'.$productImage->getFilenameBig();
        $this->filesystemWorker->remove($bigFilePath);

        $product = $productImage->getProduct();
        $product->removeProductImage($productImage);

        $this->entityManager->flush();
    }
}