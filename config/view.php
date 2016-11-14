<?php

use Jenssegers\Agent\Agent ;
$agent = new Agent();

// agent detection influences the view storage path
if ($agent->isTablet()) {
    $device = $agent->device();
    if($device == 'iPad')
    {
        $viewPath = 'desktop';  /*uncommnent */
       // $viewPath = 'mobile';
    }else{
        $viewPath = 'mobile';
    }

} elseif($agent->isMobile()){
    $viewPath = 'mobile';
}else{
    $viewPath = 'desktop'; /*uncommnent */
   // $viewPath = 'mobile';
    
    
}

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        realpath(base_path('resources/views/'.$viewPath)),
        realpath(base_path('resources/views/email-templates')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => realpath(storage_path('framework/views')),

];
