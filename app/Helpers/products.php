<?php

if (!function_exists('productsSort')) {

    /**
     * product sort helper
     *
     * products sorting
     *
     * @param bool|string $sortby
     * @return string
     */
    function productsSort($sortby = false)
    {

        if(isset($_SERVER['QUERY_STRING']))
        {
            parse_str($_SERVER['QUERY_STRING'], $qry_str);
        }else{
            $qry_str = array();
        }


       // dd($qry_str);



        if(array_key_exists('sort-order', $qry_str) && array_key_exists('sort-by', $qry_str) && $qry_str['sort-by'] ==$sortby)
        {

            $str    = '<a class="pull-right" href="';
            if($qry_str['sort-order']=='asc' && $qry_str['sort-by'] == $sortby)
            {
                $img = 'sort_desc.png';
                $qry_str['sort-order'] = 'desc';

            }elseif($qry_str['sort-order']=='desc' && $qry_str['sort-by'] == $sortby){
                $img = 'sort_asc.png';
                $qry_str['sort-order'] = 'asc';

            }else {
                $img = 'sort_both.png';

            }

            $str    .= url("products?".http_build_query($qry_str));
            $str    .= '">';
            $str    .= '<img src="'.url("assets/img/$img").'"> </a>';
        }else{


            $qry_str['sort-by'] = $sortby;
            $qry_str['sort-order'] = 'asc';
            $str    = '<a class="pull-right" href="';
            $img = 'sort_both.png';
            $str    .= url("products?".http_build_query($qry_str));
            $str    .= '">';
            $str    .= '<img src="'.url("assets/img/$img").'"> </a>';

        }
        return $str;

    }
}
if(!function_exists('limit_paragraph')){

    function limit_paragraph($x, $length)
    {
        if(strlen(strip_tags($x))<=$length)
        {
            echo $x;
        }
        else
        {
            $y=substr(strip_tags($x),0,$length) . '...';
            echo $y;
        }
    }

}

if(!function_exists('waitList'))
{
    function waitList($meta)
    {
        $wait_list = array();
        foreach($meta as $item)
        {
            if(!empty($item) && $item->meta_value!='')
            {
                $wait_list = unserialize($item->meta_value);
                foreach ($wait_list as $user)
                {
                    
                }
            }
        }

        return $wait_list;
    }
}

if(!function_exists('validPrice'))
{
    function validPrice($price = false)
    {
        if($price)
        {
            if($price!=''  && $price != '0' && $price !='0.00' )
            {
                return true;
            }
        }

        return false;
    }
}

if(!function_exists(''))
{
    function priceFormat($price = false)
    {
        if($price)
        {
            return number_format ( $price , 2 , "," , "." );
        }

        return 0.00; 

    }
}

?>