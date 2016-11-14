<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderNotes extends BaseModel
{
    public $table = "order_notes";
    public $timestamps = false;
    protected $fillable = [
        'order_id', 'created_at', 'note','type'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function order()
    {
        return $this->belongsTo('App\Orders', 'order_id', 'id');
    }

}
