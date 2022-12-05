<?php

namespace App;

use App\Exceptions\FileMarkDownDoesNotExist;
use Illuminate\Support\Facades\File;

class Document
{
    public function get($version, $page)
    {
        $path = $this->getPath($version, $page);
        if (!File::exists($path)) {
           throw new FileMarkDownDoesNotExist();
        }

        return (new \Parsedown())->text(File::get($path));
    }

    public function getPath($version, $page)
    {
//        if (app()->environment('testing')) {
//            return base_path("tests/Fixture/docs/$version/$page.md");
//        }

        return resource_path("docs/$version/$page.md");
    }

    public static function validVersions()
    {
        return ['9.x', '8.x'];
    }
}
