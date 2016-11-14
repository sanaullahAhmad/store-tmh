<?php

namespace App\Http\Controllers\wssn;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EllipseSynergie;

use App\Http\Requests;
use App\ApiKeys;
use EllipseSynergie\ApiResponse\Contracts\Response;

class WssnController extends Controller
{

    protected $apiKeys;

    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->apiKeys = new ApiKeys();
    }



    public function authenticate($request = null)
    {

        if($request === null)
            return false;

        // check if header contains consumer key and consumer secret
        if(!$request->hasHeader('consumer-key') || !$request->hasHeader('consumer-secret'))
        {
            return $this->response->withError('Klant geheim is ongeldig','woocommerce_api_authentication_error')->content();
        }

        $consumer_key       = $request->header('consumer-key');
        $consumer_secret    = $request->header('consumer-secret');


        // check if api keys exist in database
        $api_db = $this->apiKeys->getApiKey($consumer_key, $consumer_secret);
        if($api_db === null)
        {
            return $this->response->withError('Klant geheim is ongeldig','woocommerce_api_authentication_error')->content();
        }



    }
    public function orders(Request $request, $id){

        dd($this->authenticate($request));

        if($id)
        {
            dd($id);
        }
        dd($request);
    }
}
