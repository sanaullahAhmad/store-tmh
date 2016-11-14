<?php

if (!function_exists('productImage')) {

    /**
     * image helper
     *
     * provides default image if empty
     *
     * @param bool|string $url
     * @return string
     */
    function productImage($size = null, $url = false)
    { 
        //no image
        if (!$size && !$url) {
            return App::make('url')->to('assets/img/placeholder.jpg');
        }elseif(!$size && $url)
        {
            $url            = 'uploads/'.Session::get('connection').'/'.$url;
            $url            = App::make('url')->to($url);
            return $url;
        }else{

            $url            =   $url;
            $images         =   Config::get('images');
            $image_size     =   $images[Session::get('connection')][$size];

            //$img_url        = App::make('url')->to($url);
            $img_url        = 'http://fashionhomerun.nl/'.$url;
            $img_path       = $url;
            $img_ext        = pathinfo($img_url, PATHINFO_EXTENSION);
            $image_url      = str_replace($img_ext, '', $img_url);
            $img_path       = str_replace($img_ext, '', $img_path);
            $image_url      = rtrim($image_url, '.');
            $img_path       = rtrim($img_path, '.');
            $image_url      = $image_url.'-'.$image_size.'.'.$img_ext;
            $img_path       = $img_path.'-'.$image_size.'.'.$img_ext;


            return $image_url;     
            if (file_exists($img_path)) {
                return $image_url;
            }else{
                return App::make('url')->to('assets/img/placeholder.jpg');
            }
        }


    }
}

 function categoryPageThumbnail($url = false)
    {
        //no image
        if (!$url) {
            return false;
        }  

        $img_url = Config::get('constants.IMG_PATH').$url;
        $img_path = $url;
        $img_ext = pathinfo($img_url, PATHINFO_EXTENSION);
        $small_thumb = str_replace($img_ext, '', $img_url);
        $img_path = str_replace($img_ext, '', $img_path);
        $small_thumb = rtrim($small_thumb, '.');
        $img_path = rtrim($img_path, '.');
        $small_thumb = $small_thumb.'-'.'186x295'.'.'.$img_ext;
        $img_path = $img_path.'-'.'186x295'.'.'.$img_ext; 
        return $small_thumb; 
    }
function categoryBlogPageThumbnail($url = false)
{
    //no image
    if (!$url) {
        return false;
    }

    $img_url = Config::get('constants.IMG_PATH').$url;
    $img_path = $url;
    $img_ext = pathinfo($img_url, PATHINFO_EXTENSION);
    $small_thumb = str_replace($img_ext, '', $img_url);
    $img_path = str_replace($img_ext, '', $img_path);
    $small_thumb = rtrim($small_thumb, '.');
    $img_path = rtrim($img_path, '.');
    $small_thumb = $small_thumb.'-'.'341x524'.'.'.$img_ext;
    $img_path = $img_path.'-'.'341x524'.'.'.$img_ext;
    return $small_thumb;
}




// small thumbnail image


if (!function_exists('productSmallThumb')) {

    /**
     * image helper
     *
     * provides default image if empty
     *
     * @param bool|string $url
     * @return string
     */
    function productSmallThumb($url = false)
    {
        //no image
        if (!$url) {
            return App::make('url')->to('assets/img/placeholder.jpg');
        }

        $admin_thumbnails       =   Config::get('adminpanel-thumbnails');


        $listing_thumb_size     =   $admin_thumbnails[Session::get('connection')]['listing'];

        $img_url = App::make('url')->to($url);
        $img_path = $url;
        $img_ext = pathinfo($img_url, PATHINFO_EXTENSION);
        $small_thumb = str_replace($img_ext, '', $img_url);
        $img_path = str_replace($img_ext, '', $img_path);
        $small_thumb = rtrim($small_thumb, '.');
        $img_path = rtrim($img_path, '.');
        $small_thumb = $small_thumb.'-'.$listing_thumb_size.'.'.$img_ext;
        $img_path = $img_path.'-'.$listing_thumb_size.'.'.$img_ext;


        if (file_exists($img_path)) {
            return $small_thumb;
        }else{
            return App::make('url')->to('assets/img/placeholder.jpg');
        }
    }
}

// end


// listing page thumbnail
if (!function_exists('listingThumb')) {

    /**
     * image helper
     *
     * provides default image if empty
     *
     * @param bool|string $url
     * @return string
     */
    function listingThumb($url = false)
    {
        //no image
        if (!$url) {
            return App::make('url')->to('assets/img/placeholder.jpg');
        }

        $admin_thumbnails       =   Config::get('adminpanel-thumbnails');


        $listing_thumb_size     =   $admin_thumbnails[Session::get('connection')]['listing'];

        $img_url = App::make('url')->to($url);
        $img_path = $url;
        $img_ext = pathinfo($img_url, PATHINFO_EXTENSION);
        $small_thumb = str_replace($img_ext, '', $img_url);
        $img_path = str_replace($img_ext, '', $img_path);
        $small_thumb = rtrim($small_thumb, '.');
        $img_path = rtrim($img_path, '.');
        $small_thumb = $small_thumb.'-'.$listing_thumb_size.'.'.$img_ext;
        $img_path = $img_path.'-'.$listing_thumb_size.'.'.$img_ext;


        if (file_exists($img_path)) {
            return $small_thumb;
        }else{
            return App::make('url')->to('assets/img/placeholder.jpg');
        }
    }
}





// add edit page featured image thumbnail
if (!function_exists('featuredThumb')) {

    /**
     * image helper
     *
     * provides default image if empty
     *
     * @param bool|string $url
     * @return string
     */
    function featuredThumb($url = false)
    {
        //no image
        if (!$url) {
            return false;
        }

        $admin_thumbnails       =   Config::get('adminpanel-thumbnails');


        $listing_thumb_size     =   $admin_thumbnails[Session::get('connection')]['add-edit-featured'];

        $img_url = App::make('url')->to($url);
        $img_path = $url;
        $img_ext = pathinfo($img_url, PATHINFO_EXTENSION);
        $small_thumb = str_replace($img_ext, '', $img_url);
        $img_path = str_replace($img_ext, '', $img_path);
        $small_thumb = rtrim($small_thumb, '.');
        $img_path = rtrim($img_path, '.');
        $small_thumb = $small_thumb.'-'.$listing_thumb_size.'.'.$img_ext;
        $img_path = $img_path.'-'.$listing_thumb_size.'.'.$img_ext;


        if (file_exists($img_path)) {
            return $small_thumb;
        }else{
            return false;
        }
    }
}



// add edit gallery thumbnail
if (!function_exists('galleryThumb')) {

    /**
     * image helper
     *
     * provides default image if empty
     *
     * @param bool|string $url
     * @return string
     */
    function galleryThumb($url = false)
    {
        //no image
        if (!$url) {
            return false;
        }

        $admin_thumbnails       =   Config::get('adminpanel-thumbnails');


        $listing_thumb_size     =   $admin_thumbnails[Session::get('connection')]['add-edit-gallery'];

        $img_url = App::make('url')->to($url);
        $img_path = $url;
        $img_ext = pathinfo($img_url, PATHINFO_EXTENSION);
        $small_thumb = str_replace($img_ext, '', $img_url);
        $img_path = str_replace($img_ext, '', $img_path);
        $small_thumb = rtrim($small_thumb, '.');
        $img_path = rtrim($img_path, '.');
        $small_thumb = $small_thumb.'-'.$listing_thumb_size.'.'.$img_ext;
        $img_path = $img_path.'-'.$listing_thumb_size.'.'.$img_ext;


        if (file_exists($img_path)) {
            return $small_thumb;
        }else{
            return false;
        }
    }
}



// add edit gallery thumbnail
if (!function_exists('imageExist')) {

    /**
     * image helper
     *
     * provides default image if empty
     *
     * @param bool|string $url
     * @return string
     */
    function imageExist($path = false)
    {
            if($path)
            {
                if (file_exists($path))
                    return true;
            }

        return false;
    }
}