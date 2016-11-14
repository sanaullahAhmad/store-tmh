<?php

namespace App;
use DB;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    public $table = "orders";
    protected $fillable = [
        'id', 'created_at', 'updated_at', 'shipping_info_id', 'billing_info_id', 'payment_method' ,'completed_at','amount','total_tax','discount','domain_id','customer_id','shipping_cost','status','client_details'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    //add routine for orders
    public function add($data){
        DB::connection($this->connection)->table('orders')->insert($data);
    }
    //added for listing all records in paginated
    public function getAll($args, $sort_by = '', $sort_order = '')
    {
        DB::connection($this->connection)->enableQueryLog();

         

        $query  = Orders::query();
        $query->with('billing_info')
                        ->with('Customer')
                        ->with('order_items')
                        ->where(function($query) use ($args){

                            foreach($args as $arg)
                            {
                                if($arg['table'] == 'customers')
                                {
                                    //SELECT * FROM `products` WHERE id = (SELECT parent_id FROM products WHERE sku =99169002656 )
                                    //SELECT * FROM `products` WHERE products.id = (SELECT components.product_id from products left JOIN components ON products.id = components.default_id WHERE products.sku = 99169000427)


                                    $query->orWhere(
                                        'orders.customer_id' ,
                                        '=' ,
                                        DB::raw("(SELECT id FROM customers WHERE 
                                                    first_name   = '".urldecode($arg['value'])."' 
                                                    OR last_name = '".urldecode($arg['value'])."' 
                                                    OR email     = '".urldecode($arg['value'])."'
                                                    OR postcode  = '".urldecode($arg['value'])."'
                                                    limit 1
                                                    )"));


                                }else{
                                    $query->Where(
                                        'orders.'.$arg['column'] ,
                                        $arg['operator'] ,
                                        $arg['value']);
                                }

                            }

                        });
        // sort by
        if($sort_by!='' && $sort_order!='')
        {
            $query->orderBy('orders.'.$sort_by, $sort_order);
        }elseif($sort_by!='' && $sort_order=='')
        {
            $sort_order == 'asc';
            $query->orderBy('orders.'.$sort_by, $sort_order);
        }else{
            $query->orderBy('orders.id', 'desc');
        }

        $orders = $query->paginate(60);
        //$orders = $query->get();
        //$orders = $query->toSql();
        //dd($orders);
        //$query = DB::connection($this->connection)->getQueryLog();
        //dd($query);
        //dd($orders);
        return $orders;
    }

    //get orders on the basis of set of arguments
    public function getOrders($args){

        $query = "SELECT id FROM orders where date(created_at) = '".$args."'";
        return DB::connection($this->connection)->select(DB::raw($query));
        //return Orders::get()->where('completed_at','=',$args);
    }

    // get order by id
    public function getOrder($id)
    {
        return Orders::with('billing_info')
                ->with('shipping_info')
                ->with('Customer')
                ->with('order_items')
                ->with('orderNotes')
                ->with('refunds.refundItems')
                ->where('id', $id)
                ->first();

    }

     

    public function shipping_info()
    {
        return $this->hasOne('App\OrderShipping', 'order_id', 'id');
    }

    public function billing_info()
    {
        return $this->hasOne('App\OrderBilling', 'order_id', 'id');
    }

    public function order_items(){
        return $this->hasMany('App\OrderItems', 'order_id', 'id');
    }

    public function customer(){
        return $this->belongsTo('App\Customers', 'customer_id', 'id');
    }

    public function orderNotes(){
        return $this->hasMany('App\OrderNotes', 'order_id', 'id');
    }

    public function refunds(){
        return $this->hasMany('App\Refunds', 'order_id', 'id');
    }


    public function ordersDates(){
        return Orders::select(DB::raw('DATE(created_at) as dates'))
            ->Where('created_at','<>','0000-00-00 00:00:00')
            ->groupBy('dates')
            ->orderBy('dates', 'desc')
            ->get();
    }



    public function bulkAction($order_ids = false, $status = false)
    {

        if($order_ids && $status)
        {
            $dataUpdate = array('status' => $status);
            foreach ($order_ids as $order_id)
            {
                Orders::where('id', $order_id)->update($dataUpdate);
            }

        }

        return true; 
    }
    
    
    public function getRefundItems($order_id, $product_id)
    {
        return DB::connection($this->connection)->table('refund_items')->where('order_id', $order_id)->where('product_id', $product_id)->get();
    }

    public function moveToTrash($order_id)
    {
        if($order_id)
        {
            $dataUpdate = array('status' => 'deleted');
            return Orders::where('id', $order_id)->update($dataUpdate);
        }
    }

}
