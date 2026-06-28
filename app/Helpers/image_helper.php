<?php

use App\Libraries\MenuImageStorage;

if (! function_exists('menu_image_url')) {
    /**
     * Resolves a stored menu-image key to its public display URL.
     *
     * Works for both the R2 and local storage drivers, so views never need to
     * know where the image actually lives.
     */
    function menu_image_url(?string $key): string
    {
        static $storage = null;
        if ($storage === null) {
            $storage = new MenuImageStorage();
        }

        return $storage->url($key);
    }
}
