<?php
namespace App\Utils\File;


use App\Utils\Filesystem\FilesystemWorker;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface; //для кириллицы в именах файлах

class FileSaver{
    /**
     * @var SluggerInterface
     */
    private $slugger;


    /**
     * @var string
     */
    private $uploadsTempDir;


    /**
     * @var FilesystemWorker
     */
    private $filesystemWorker;


    public function __construct(SluggerInterface $slugger, FilesystemWorker $filesystemWorker, string $uploadsTempDir)
    {
        $this->slugger = $slugger;
        $this->uploadsTempDir = $uploadsTempDir;
        $this->filesystemWorker = $filesystemWorker;
    }


    /**
     * @param UploadedFile $uploadedFile
     * @return string|null
     */
    public function saveUploadedFileIntoTemp(UploadedFile $uploadedFile){
        $originalFilename = pathinfo( $uploadedFile->getClientOriginalName(), PATHINFO_FILENAME ); //"screen_9" из "screen_9.jpg"
        $saveFileName = $this->slugger->slug( $originalFilename );
        $filename = sprintf('%s-%s.%s', $saveFileName,uniqid(), $uploadedFile->getClientOriginalExtension());
        //uniqid - чтобы не было дубликатов изображений
        //dd($filename,$this->uploadsTempDir); //"D:\code\sites\OSPanel\domains\ranked-choice.shop/public/uploads/temp"

        $this->filesystemWorker->createFolderIfItNotExist($this->uploadsTempDir);

        try{
            $uploadedFile->move( $this->uploadsTempDir,$filename);
        }catch(\Exception $exception){
            return null; //нужно было бы записать в логи
        }

        return $filename;
    }


}