<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::group(['middlewareGroups' => ['web']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::get('/category/{slug}', array('as' => 'category.show', 'uses' => 'CategoryController@index'));
    Route::get('/belowfold/{slug}', 'CategoryController@belowfold');
    Route::get('/getcat_sidebar_prod_slider/{slug}', 'CategoryController@getcat_sidebar_prod_slider');
    Route::get('/getcategoryproducts/{slug}/{itemsPerPage}/{pagenumber}', array( 'uses' => 'CategoryController@getCategoryProducts'));

    Route::get('/post-category/{slug}', array( 'uses' => 'BlogController@index'));
    Route::get('/post/{slug}', array( 'uses' => 'BlogController@showPost'));
    Route::get('/page/{slug}', array( 'uses' => 'BlogController@showPage'));
    Route::post('/contact/', array( 'uses' => 'BlogController@SubmitContactForm'));



    Route::get('/getmainslideriamges', 'HomeController@getMainSliderImages');
    Route::get('/getproductssliderimages', 'HomeController@getProductsSliderImages');

    Route::get('/product/{slug}/','ProductDetailsController@index');
    Route::get('/product/crosssells/{slug}','ProductDetailsController@crosssells');

    Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);
     
    Route::get('login', 'AuthController@getLogin');
    Route::post('login', 'AuthController@postLogin');  

    Route::post('register', ['as' => 'register', 'uses' => 'AuthController@postRegister']);

    // Password Reset Routes... 
    Route::get('passwordreset',                     'PasswordController@getEmail');
    Route::post('password/email',                   'PasswordController@sendResetLinkEmail'); 
    Route::get('resetpassword/{email}/{token}',     'PasswordController@getReset'); 
    Route::post('password/resetact',                'PasswordController@reset');
    //Route::get('password/verify',   'PasswordController@verify');

    Route::get('/mijn-account', 'CustomersController@index');
    Route::get('/miwishlist', 'WishlistController@index');
    Route::get('/addtowishlist/{product_id}', 'WishlistController@addtowishlist');
    Route::get('/removefromwishlist/{product_id}', 'WishlistController@removefromwishlist');
    Route::get('/tryouts_like/{product_id}/{like}', 'CategoryController@tryouts_like');

    //added for add to cart routine
    Route::post('addtocart/','ProductDetailsController@addtocart');
    Route::get('/winkelwagen','CartController@index');
    Route::post('/cartcheck','CartController@cart_count');

    //Route::post('add_to_wait_list, ProductDetailsController@addToWaitList');
    Route::get('/add_to_wait_list/{pid}/{email}', 'WaitListController@addToWaitList');

    ///////////////////////////////   WSSN api routes   //////////////////////////////////////
    Route::group(array('prefix' => '/wc-api/v3'), function()
    {
        Route::match(['get', 'post'], '/orders/{id?}',[
            'uses' => 'wssn\WssnController@orders'
        ]);
    });
    //Route::get('/wc-api/v3/orders/{id?}', 'wssn\WssnController@orders');





}); 



Route::group(['middleware' => ['auth']], function () { 
    
    Route::post('/updatebillingaddress', 'CustomersController@updateBillingAddress');  
    Route::post('/updateshippingaddress', 'CustomersController@updateShippingAddress');  
    Route::post('/resetmypassword', 'CustomersController@resetMyPassword');  
});

 