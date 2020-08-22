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
    protected $ClientId;
    protected $ClientSecret;
    protected $refreshToken;

    public function __construct()
    {
        
        $this->client = new \Google_Client();
        
        $this->client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
        $this->client->refreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
       
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