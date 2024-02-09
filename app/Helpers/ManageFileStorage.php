<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ManageFileStorage
{
    public static function delete($location)
    {
        if($location)
        {
            Storage::delete($location);
        }
    }
}

