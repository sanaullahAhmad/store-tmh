<?php

namespace App;
use DB; 

class OrderItems extends BaseModel
{
    protected $fillable = [
        'order_id', 'product_id', 'unit_price', 'unit_tax', 'qty', 'total' ,'total_tax'
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    //add routine for payment information of orders
    public static function add($data){
        DB::table('order_items')->insert($data);
    }

    public function getItemsAvg($args){
        return DB::connection($this->connection)->table('order_items')
            ->select(DB::raw('sum(qty) as product_count, product_id'))
            ->whereIn('order_id', $args)
            ->groupBy('product_id')
            ->get();

    }

    public function order()
    {
        return $this->belongsTo('App\Orders', 'order_id', 'id');
    }

     

    
}
