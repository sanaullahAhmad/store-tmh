<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class ApiKeys extends Model
{
    public $table = "apikeys";
    public $timestamps = false;
    protected $fillable = ['description', 'user_id', 'permissions', 'consumer_key', 'consumer_secret', 'created_at', 'last_access'];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function user(){
        return $this->belongsTo('App\User');
    }


    public function getApiKeys()
    {
        return ApiKeys::with('user')->get();
    }

    public function getApiKeysById($id = null)
    {
        if($id)
        {
            return ApiKeys::with('user')->where('id', $id)->first();
        }
    }

    public function updateApiKeyById($input , $id)
    {
        if($input && $id)
        {
            $desc           = trim($input['description']);
            $user_id      = $input['user'];
            $permissions      = $input['permissions'];

            $dataUpdate = array(
                'description'       => $desc,
                'user_id'           => $user_id,
                'permissions'       => $permissions
            );

            return ApiKeys::where('id', $id)->update($dataUpdate);
        }else{
            return false;
        }
    }

    /////////////////////// wssn functions /////////////////////////

    public function getApiKey($consumer_key = null, $consumer_secret = null)
    {
        if($consumer_key && $consumer_secret)
        {
            $keys = ApiKeys::where('consumer_key', $consumer_key)->where('consumer_secret', $consumer_secret)->first() ;
            if($keys !== null) {
                return true;
            }
        }

        return false;
    }



}
