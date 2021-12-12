<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Utils\Manager\ProductImageManager;
use App\Entity\Product;

Class ProductManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Utils\Manager\ProductImageManager
     */
    private $productImageManager;

    /**
     * @var string
     */
    private $productImageDir;


    public function __construct(EntityManagerInterface $entityManager, ProductImageManager $productImageManager, string $productImagesDir)
    {
        $this->entityManager = $entityManager;
        $this->productImagesDir = $productImagesDir;
        $this->productImageManager = $productImageManager;
    }

    public function getRepository() : ObjectRepository
    {
        //чтобы постоянно не обращаться к репозиторию
        return $this->entityManager->getRepository(Product::class);
    }


    /**
     * @param Product $product
     */
    public function save(Product $product)
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /**
     * @param Product $product
     */
    public function remove(Product $product)
    {
        $product->setIsDeleted(true);
        $this->save($product);
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductImagesDir(Product $product){
        return sprintf('%s/%s', $this->productImagesDir, $product->getId());
    }

    public function updateProductImages(Product $product, string $tempImageFilename = null) : Product
    {
        if(!$tempImageFilename){
            return $product;
        }

        $productDir = $this->getProductImagesDir($product);

        $productImage = $this->productImageManager->saveImageForProduct($productDir, $tempImageFilename);
        $productImage->setProduct($product);
        $product->addProductImage($productImage);

        return $product;
    }
}