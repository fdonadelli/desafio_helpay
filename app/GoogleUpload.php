<?php

namespace App;

use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;

class GoogleUpload
{
    protected $client;
    protected $folder_id;
    protected $service;
    protected $ClientId     = '974850162182-v3igc16e18olh2lrf9bf545rgcm6apfh.apps.googleusercontent.com';
    protected $ClientSecret = 'aOhMyKpbwPDf8Z52Br07j23I';
    protected $refreshToken = '1//04UeInXpil7xcCgYIARAAGAQSNwF-L9IrGHXrfRwFBBdzE9c9XfcEe5gQxsASCwtMfGePMyTTQ4N4R33Fs16S6o2rSvEPJHJyRVk';

    public function __construct()
    {
        
        $this->client = new \Google_Client();
        
        $this->client->setClientId($this->ClientId);
        $this->client->setClientSecret($this->ClientSecret);
        $this->client->refreshToken($this->refreshToken);
       
        $this->service = new \Google_Service_Drive($this->client);
        // we cache the id to avoid having google creating
        // a new folder on each time we call it,
        // because google drive works with 'id' not 'name'
        // & thats why u could have duplicated folders under the same name
        Cache::rememberForever('new_folder_id', function () {
            return $this->create_folder();
        });
        
        $this->folder_id = Cache::get('new_folder_id');
    }

    public function getListFiles(){
        $response = $this->service->files->listFiles();
        return $response;
    }

    public function generateUrl(){
        $id = $this->getListFiles()->files[0]->id;
        $url = "https://drive.google.com/file/d/$id/view?usp=sharing";
        return $url;
    }
    
}