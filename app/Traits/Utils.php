<?php

namespace App\Traits;


trait Utils {

    protected function generateRef($slug = '' , $length = 16)
    {
        return $slug.substr(bin2hex(random_bytes($length)),0, $length);
    }
}


?>