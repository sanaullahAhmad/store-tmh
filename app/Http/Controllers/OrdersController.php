<?php

namespace App\Http\Controllers;
use App\Countries;
use App\OrderBilling;
use App\OrderShipping;
use App\Products;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Orders;
use App\OrderNotes;
use Session;
use DB;


class OrdersController extends BaseController
{

    protected $orders;
    protected $countries;
    protected $orderNotes;
    protected $connection;
    public function __construct()
    {
        parent::__construct(); 
        $this->orders = new Orders();
        $this->countries = new Countries();
        $this->orderNotes = new OrderNotes();

        $this->connection = Session::get('connection');
    }


    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // orders list page

        $request_all = $request->all();

        $orders_obj = new Orders();

        // search arguments
        $args = array();

        if(isset($request_all['keywords']) && $request_all['keywords']!='')
        {

            if(is_numeric($request_all['keywords']))
            {
                $args['keywords']['table'] 	    = 'orders';
                $args['keywords']['column'] 	= 'id';
                $args['keywords']['operator']	= 'like';
                $args['keywords']['value']	    = "%".str_replace('+','',$request_all['keywords'])."%";
            }else{
                $args['keywords']['table'] 	    = 'customers';
                $args['keywords']['column'] 	= 'customers';
                $args['keywords']['operator']	= '=';
                $args['keywords']['value']	    = str_replace('+','',urlencode($request_all['keywords']));
            }

        }


        if(isset($request_all['date_from']) && $request_all['date_from']!='' )
        {

            $search_date_from 			= urldecode($request_all['date_from']);
             
            if($search_date_from != ' ') {
                $from_date = date('Y-m-d H:i:s', strtotime(trim($search_date_from)));
                $args['date_from']['table'] = 'orders';
                $args['date_from']['column'] = 'created_at';
                $args['date_from']['operator'] = '>=';
                $args['date_from']['value'] = $from_date;

            }
        }

        if(isset($request_all['date_to']) && $request_all['date_to']!='' )
        {

            $search_date_to 			= urldecode($request_all['date_to']);

            if($search_date_to != ' ') {
                $to_date = date('Y-m-d 59:59:59', strtotime(trim($search_date_to)));

                $args['date_to']['table'] = 'orders';
                $args['date_to']['column'] = 'created_at';
                $args['date_to']['operator'] = '<=';
                $args['date_to']['value'] = $to_date;
            }
        }



        if(isset($request_all['status']) && $request_all['status']!='')
        {

            $status = $request_all['status'];
            $args['status']['table'] 		= 'orders';
            $args['status']['column'] 		= 'status';
            $args['status']['operator']		= '=';
            $args['status']['value']		= $status;
        }else{
            $args['status']['table'] 		= 'orders';
            $args['status']['column'] 		= 'status';
            $args['status']['operator']		= '<>';
            $args['status']['value']		= 'deleted';
        }



        if(isset($request_all['sort-by']) && $request_all['sort-by']!='')
        {
            $sort_by = $request_all['sort-by'];
        }else{
            $sort_by = 'id';
        }

        if(isset($request_all['sort-order']) && $request_all['sort-order']!='')
        {
            $sort_order = $request_all['sort-order'];
        }else{
            $sort_order = 'desc';
        }

        $filter_fields = $this->buildFilterFields();

        $orders = $orders_obj->getAll($args, $sort_by, $sort_order);
         
        $counts = $orders_obj->getOrderCounts();



        return view('orders.orders', ['orders' => $orders , 'counts' =>$counts, 'fields' => $filter_fields]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //edit or view order
        $order = $this->orders->getOrder($id);
        //dd($order);
        $countries  = $this->countries->getCountries(); 

        return view('orders.editorder', ['order' => $order, 'countries' => $countries]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
       // dd($request->input());
        $input              = $request->input();
        $order_data         = array( 'status' => $input['order_status'], 'customer_id'  => $input['order_customer']) ;
        $shipping_data      = array(
                                    'first_name'        => $input['shipping_fname'],
                                    'last_name'         => $input['shipping_lname'],
                                    'address_1'         => $input['shipping_address1'],
                                    'address_2'         => $input['shipping_address2'],
                                    'city'              => $input['shipping_city'],
                                    'postcode'          => $input['shipping_postcode'],
                                    'country'           => $input['shipping_country']
                                    );


        $billing_data      = array(
            'first_name'        => $input['billing_fname'],
            'last_name'         => $input['billing_lname'],
            'address_1'         => $input['billing_address1'],
            'address_2'         => $input['billing_address2'],
            'city'              => $input['billing_city'],
            'postcode'          => $input['billing_postcode'],
            'country'           => $input['billing_country'],
            'phone'             => $input['billing_phone'],
            'email'             => $input['billing_email']
        );

        DB::connection($this->connection)->beginTransaction();

        try {

            Orders::where('id', $id)->update($order_data);
            OrderShipping::where('order_id', $id)->update($shipping_data);
            OrderBilling::where('order_id', $id)->update($billing_data);

            DB::connection($this->connection)->commit();
        }

        catch (\Exception $e) {


            DB::connection($this->connection)->rollback();
            echo $e->getMessage();
            echo $e->getCode();
            return $e->getCode();

        }

        return redirect()->action('OrdersController@edit', ['id' =>  $id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->orders->moveToTrash($id))
        {
            Session::flash('flash_message', 'Order Successfully Move to Trash!');
            return redirect('/orders');
        }else{
            Session::flash('flash_message', 'Error: Unable to Moved to Trash!');
            return redirect('/orders');
        }
    }



    public function bulkAction(Request $request)
    {

        if($request->input('bulk-action')  && !empty($request->input('bulk_orders')))
        {
            $orders_obj = new Orders();
            if($orders_obj->bulkAction($request->input('bulk_orders'), $request->input('bulk-action')))
            {
                switch ($request->input('bulk-action'))
                {
                    case 'deleted':
                        Session::flash('flash_message', 'Orders Successfully Moved to trash!');
                        break;
                    case 'complete':
                        Session::flash('flash_message', 'Orders Successfully Marked as Complete!');
                        break;
                    case 'pending':
                        Session::flash('flash_message', 'Orders Successfully Marked as Pending!');
                        break;
                }

                return redirect('/orders');
            }
        }else {

            Session::flash('flash_warning', 'No orders were selected for bulk action');
            return redirect('/orders');
        }

    }


    public function changeStatus($id = false, $status = false)
    {

        if($id && $status) {
            $orders_obj = new Orders();
            if ($orders_obj->bulkAction([$id], $status))
            {
                Session::flash('flash_message', 'Order Status Changed Successfully!');
                return redirect('/orders');
            }
        }
        Session::flash('flash_warning', 'Error changing order status');
        return redirect('/orders');
    }


    //////////////////
    //
    // controller helper functions
    //
    //////////////////


    // build fields html from query string for search filter
    protected function buildFilterFields()
    {
        $str = '';
        if(isset($_GET['sort-by']))
        {
            $str .= '<input type="hidden" name="sort-by" value="'.$_GET['sort-by'].'" >';
        }

        if(isset($_GET['sort-order']))
        {
            $str .= '<input type="hidden" name="sort-order" value="'.$_GET['sort-order'].'" >';
        }

        if(isset($_GET['status']))
        {
            $str .= '<input type="hidden" name="status" value="'.$_GET['status'].'" >';
        }



        return $str;
    }

    public function SaveOrderNote(Request $request)
    {
        $input = $request->input();
        if($input['order_id'])
        {
            $now = date('Y-m-d H:i:s');
            $data = array('order_id' => $input['order_id'], 'created_at' => $now, 'note' => $input['note']);
            if(OrderNotes::create($data))
            {
                $lastInsertedId = DB::connection($this->connection)->getPdo()->lastInsertId();
                echo json_encode(array('action' => true, 'date' => $now, 'note_id' => $lastInsertedId, 'note' => $input['note']));
                exit;
            }
        }

        echo json_encode(array('action' => false, 'msg' => 'Error saving note!'));
        exit;
    }


    public function downloadInvoice($id)
    {

        if($order = $this->orders->getOrder($id)->toArray())
        {

            foreach ($order['order_items'] as $item)
            {
                if($products = Products::where('id', $item['product_id'])->first())
                {
                    $order['products'][$item['product_id']] = $products->toArray();
                }

            }
            //dd($order);

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadView('pdf.order_invoice', $order);
            //return $pdf->stream();
            return $pdf->download('invoice.pdf');
        }
    }

}





