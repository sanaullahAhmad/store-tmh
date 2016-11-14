<?php

namespace App\Http\Controllers;
use App\Menu;
use App\MenuDetails;
use Illuminate\Http\Request;

use App\Http\Requests;
use Jenssegers\Agent\Agent ;

class BaseController extends Controller
{

    protected $agent;
    protected $menu;
    protected $device;
    protected $deviceName;

    public function __construct()
    {
        $this->agent        = new Agent();
        $this->menu         = $this->buildMenu();
        $this->footerMenu   = $this->buildFooterMenu();
        $this->device       = $this->device();
        $this->deviceName   = $this->agent->device();
    }



    public function buildMenu()
    {
        $main_menu = [array('submenu' => [])];

        $menu = Menu::where('is_primary', 'yes')->first();
        if($menu)
        {
            $menu_detail = MenuDetails::where('menu_id', $menu->id)->get()->toArray();

            if(!empty($menu_detail))
            {
                foreach ($menu_detail as $menu_item)
                {
                    if($menu_item['parent_id'] == 0)
                    {
                        $main_menu[$menu_item['id']] = $menu_item;
                        foreach ($menu_detail as $single_item)
                        {
                            if($single_item['parent_id'] == $menu_item['id'])
                            {
                                $main_menu[$menu_item['id']]['submenu'][] =  $single_item ;
                            }
                        }
                    }

                }

                unset($main_menu[0]);
                return $main_menu;
            }

        }

        return [];
    }

    public function buildFooterMenu()
    {
        $footerMenu     = array(
                                    0=> array( 
                                        "id" => 31,     
                                        "menu_id" => 1     ,
                                        "sub_menu_id" => 33 ,     
                                        "parent_id" => 0     ,
                                        "url" => "#"     ,
                                        "title" => "The Musthaves",     
                                        "type" => "category"   ,   
                                        "submenu" => array(  
                                                          0 => array( 
                                                                  "id" => 32,
                                                                   "menu_id" => 1,
                                                                   "sub_menu_id" => 8836,
                                                                   "parent_id" => 31,
                                                                   "url" => "/page/over-ons",
                                                                   "title" => "Over Ons",
                                                                   "type" => "page"
                                                                   ),

                                                          1 => array(
                                                                  "id" => 33,
                                                                  "menu_id" => 1,
                                                                  "sub_menu_id" => 7590,
                                                                  "parent_id" => 31,
                                                                  "url" => "/page/nieuwsbrief",
                                                                  "title" => "Nieuwsbrief",
                                                                  "type" => "page"
                                                                  ),
                                                          2 => array(
                                                                  "id" => 33,
                                                                  "menu_id" => 1,
                                                                  "sub_menu_id" => 7590,
                                                                  "parent_id" => 31,
                                                                  "url" => "/page/media",
                                                                  "title" => "Media",
                                                                  "type" => "page"
                                                                  ),
                                                          3 => array(
                                                                  "id" => 33,
                                                                  "menu_id" => 1,
                                                                  "sub_menu_id" => 7590,
                                                                  "parent_id" => 31,
                                                                  "url" => "/page/happy-customers",
                                                                  "title" => "Happy Customers",
                                                                  "type" => "page"
                                                                  ),
                                                            4 => array(
                                                                "id" => 33,
                                                                "menu_id" => 1,
                                                                "sub_menu_id" => 7590,
                                                                "parent_id" => 31,
                                                                "url" => "/page/blogger",
                                                                "title" => "Blogger / Samenwerking",
                                                                "type" => "page"
                                                            )
                                                          ) 
                                    ),
                                    1=> array( 
                                        "id" => 31,     
                                        "menu_id" => 1     ,
                                        "sub_menu_id" => 33 ,     
                                        "parent_id" => 0     ,
                                        "url" => "#"     ,
                                        "title" => "Services",     
                                        "type" => "category"   ,   
                                        "submenu" => array(  
                                                          0 => array( 
                                                                  "id" => 32,
                                                                   "menu_id" => 1,
                                                                   "sub_menu_id" => 8836,
                                                                   "parent_id" => 31,
                                                                   "url" => "/page/vraag-en-antwoord",
                                                                   "title" => "Vraag & Antwoord",
                                                                   "type" => "page"
                                                                   ),
                                                          1 => array( 
                                                                  "id" => 33,
                                                                  "menu_id" => 1,
                                                                  "sub_menu_id" => 7590,
                                                                  "parent_id" => 31,
                                                                  "url" => "/page/bestelling-levering",
                                                                  "title" => "Bestelling & Levering",
                                                                  "type" => "page"
                                                                  ),
                                                            2 => array(
                                                                "id" => 33,
                                                                "menu_id" => 1,
                                                                "sub_menu_id" => 7590,
                                                                "parent_id" => 31,
                                                                "url" => "/page/betalen",
                                                                "title" => "Betalen",
                                                                "type" => "page"
                                                            ),
                                                          3 => array(
                                                                  "id" => 33,
                                                                  "menu_id" => 1,
                                                                  "sub_menu_id" => 7590,
                                                                  "parent_id" => 31,
                                                                  "url" => "/page/retouren",
                                                                  "title" => "Retouren & Ruilen",
                                                                  "type" => "page"
                                                                  ),
                                                          4 => array(
                                                                  "id" => 33,
                                                                  "menu_id" => 1,
                                                                  "sub_menu_id" => 7590,
                                                                  "parent_id" => 31,
                                                                  "url" => "/page/klacht-garantie",
                                                                  "title" => "Klacht & Garantie",
                                                                  "type" => "page"
                                                                  ),

                                                          ) 
                                    ),
                                    2=> array( 
                                        "id" => 31,     
                                        "menu_id" => 1     ,
                                        "sub_menu_id" => 33 ,     
                                        "parent_id" => 0     ,
                                        "url" => "#"     ,
                                        "title" => "ALgemeen",     
                                        "type" => "category"   ,   
                                        "submenu" => array(  
                                                          0 => array( 
                                                                  "id" => 32,
                                                                   "menu_id" => 1,
                                                                   "sub_menu_id" => 8836,
                                                                   "parent_id" => 31,
                                                                   "url" => "/page/vacatures",
                                                                   "title" => "Vacatures",
                                                                   "type" => "page"
                                                                   ),
                                                          1 => array( 
                                                                  "id" => 33,
                                                                  "menu_id" => 1,
                                                                  "sub_menu_id" => 7590,
                                                                  "parent_id" => 31,
                                                                  "url" => "/page/algemene-voorwaarden",
                                                                  "title" => "Algemene Voorwaarden",
                                                                  "type" => "page"
                                                                  ),
                                                          2 => array( 
                                                                  "id" => 33,
                                                                  "menu_id" => 1,
                                                                  "sub_menu_id" => 7590,
                                                                  "parent_id" => 31,
                                                                  "url" => "/page/privacybeleid ",
                                                                  "title" => "Privacybeleid",
                                                                  "type" => "page"
                                                                  ),

                                                          3 => array(
                                                                  "id" => 33,
                                                                  "menu_id" => 1,
                                                                  "sub_menu_id" => 7590,
                                                                  "parent_id" => 31,
                                                                  "url" => "/page/mijn-account",
                                                                  "title" => "Mijn Account",
                                                                  "type" => "page"
                                                                  )
                                                          ) 
                                    ),
                                    3=> array( 
                                        "id" => 31,     
                                        "menu_id" => 1     ,
                                        "sub_menu_id" => 33 ,     
                                        "parent_id" => 0     ,
                                        "url" => "#"     ,
                                        "title" => "Contact",     
                                        "type" => "category"   ,   
                                        "submenu" => array(
                                                        0 => array(
                                                            "id" => 33,
                                                            "menu_id" => 1,
                                                            "sub_menu_id" => 7590,
                                                            "parent_id" => 31,
                                                            "url" => "/page/contact",
                                                            "title" => "Contact",
                                                            "type" => "page"
                                                        )
                                                          ) 
                                    )

                                );  

        return $footerMenu; 
    }

    public function device()
    {
        if ($this->agent->isTablet()) {

            $deviceType = 'tablet';

        } elseif($this->agent->isMobile()){

            $deviceType = 'mobile';

        }else{

            $deviceType = 'desktop';
        }

        return $deviceType;
    }

}
