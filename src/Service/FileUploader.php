<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private string $targetDirectory;
    private SluggerInterface $slugger;

    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {   
        // string $imageDirectory,string $bannerDirectory,
        $this->targetDirectory = $targetDirectory;
        // $this->imageDirectory = $imageDirectory;
        // $this->bannerDirectory = $bannerDirectory;


        $this->slugger = $slugger;
    }
    
    // public function uploadBanner(UploadedFile $file): string
    // {
    //     // Générer un nom unique pour le fichier
    //     $newFileName = md5(uniqid()) .'.'  . $file->guessExtension();
    //     $file->move($this->getBannerDirectory(), $newFileName);

    //     return $newFileName;
    // }

    public function upload(UploadedFile $file): string
    {
        // Générer un nom unique pour le fichier
        $newFileName = md5(uniqid()) .'.'  . $file->guessExtension();
        $file->move($this->getTargetDirectory(), $newFileName);

        return $newFileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
