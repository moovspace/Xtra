<?php
namespace Xtra\Menu;
use \Exception;

class Menu
{
    private $Links = array();

    public function AddLink($slug, $name, $icon = '', $class = '', $url = ''){
        $slug = '/'.ltrim($slug,'/');
        $this->SubSlug = $slug;
        $this->Links[] = array('slug' => $slug, 'subslug' => $this->SubSlug, 'name' => $name, 'icon' => $icon, 'class' => $class, 'url' => $url);
    }

    public function AddSubLink($slug, $name, $icon = '', $class = '', $submenu = null){
        $slug = $this->SubSlug.'/'.ltrim($slug,'/');
        $this->Links[] = array('slug' => $slug, 'subslug' => $this->SubSlug, 'name' => $name, 'icon' => $icon, 'class' => $class, 'submenu' => $submenu);
    }

    public function MixParts(){
        $part = "";
        for ($i=0; $i < count($this->UrlParts); $i++) {
            $part .= '/'.$this->UrlParts[$i];
            $this->UrlPart[] = $part;
        }
        return $this->UrlPart;
    }

    public function CleanUrl($url = ""){
        return str_replace('//','/',strtolower($url));
    }

    public function GetCurrentUrl(){
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    function GetIcon($ico){
        $i = "";
        if(!empty($ico)){
            $i = '<i class="'.$ico.'"></i>';
        }
        return $i;
    }

    function GetMenu($sub = false, $menu_part = "menu-part"){
        // Cut parts
        $this->MixParts();
        $curl = $this->GetCurrentUrl();
        // Menu
        $html = '<div class="'.$menu_part.'">';
        foreach ($this->Links as $k => $v) {
            if($v['slug'] == $v['subslug']){
                // Main link
                $url = $this->CleanUrl($v['slug']);
                if(!empty($v['url'])){
                    // Redirect url
                    $url = $this->CleanUrl($v['url']);
                }
                // Zawsze widoczne if false
                if($sub == false){
                    // Add icon
                    $icon = $this->GetIcon($v['icon']);
                    // Highlight
                    $active = 'link-inactive';
                    // if($curl == $this->CleanUrl($v['slug'])){
                    // if($v['slug'] != '/'){
                        if($curl == $v['slug']){
                            $active = 'link-active';
                        }else if(strpos("x".$curl,$v['slug']) > 0 && $v['slug'] != '/'){
                            $active = 'link-active';
                        }
                    // }
                    // Create link
                    $html .= '<a class="main-link main-'.$active.' '.$active.' '.$v['class'].'" href="'.$url.'"> '.$icon.' '.$v['name'].'</a>';
                }
                // Open submenu
                $html .= $this->GetSubLinks($v['slug']);
            }
        }
        return $html.'</div>';
    }

    function GetSubLinks($slug = ""){
        $curl = $this->GetCurrentUrl();
        $active = 'hide-part';
        // Dsiplay submenu
        if($slug == $curl || strpos("x".$curl,$slug) > 0){
            $active = 'show-part';
        }
        $html = '<div class="submenu-part submenu-'.$active.' '.$active.'">';
        foreach ($this->Links as $k => $v) {
            if($v['slug'] != $v['subslug']){
                if($v['subslug'] == $slug || $v['slug'] == $slug){
                    // Icon
                    $icon = $this->GetIcon($v['icon']);
                    // Active
                    $url = $this->CleanUrl($v['slug']);
                    $active = 'link-inactive';
                    if($curl == $url){
                        $active = 'link-active';
                    }
                    // Link
                    $html .= '<a class="sub-link '.$active.' '.$v['class'].'" href="'.$url.'"> '.$icon.' '.$v['name'].'</a>';
                    // Next submenu
                    if($v['submenu'] != null){
                        $html .= $v['submenu']->GetMenu(true);
                    }
                }
            }
        }
        return $html.'</div>';
    }

    function CssStyle(){
        echo '
        <style>
        html,body{margin: 0px; padding: 0px;}
        a{float: left; width: 100%; padding: 9px 15px; font-size: 15px; font-family: arial; text-decoration: none; color: #222;}
        a i{margin-right: 10px}
        .menu-part{float: left; width: 250px}
        .menu-part *:not(i){font-family: \'Open Sans\', sans-serif; box-sizing: border-box}
        .submenu-part{float: left; width: 100%}
        .submenu-part .menu-part{float: left; width: 100%;}
        .submenu-part .menu-part .submenu-part{background: transparent;}
        .main-link{background: #fff; padding: 5px;}
        .link-active{padding: 9px 15px; float: left; width: 100%; font-weight: 700; background: rgba(0, 153, 255, .05); color: #09f; border-radius: 0px 90px 90px 0px !important}
        .link-inactive{padding: 9px 15px; float: left; width: 100%; font-weight: 400;}
        .show-part{display: inherit}
        .hide-part{display: none}
        .menu-part > .submenu-show-part{background: transparent}
        .submenu-show-part .main-link{background: #f90; color: #fff}
        .main-link-active{background: rgba(0, 153, 255, .9); color: #fff; border-radius: 0px !important; font-weight: 700}
        .submenu-show-part {padding-left: 0px}
        </style>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap&subset=latin-ext" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
        ';
    }
}

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

try{
    // SUBMENU 3
    $m3 = new Menu();
    $m3->AddLink('settings/profil/sub2/user','User Submenu');
    $m3->AddSubLink('user1','User 1', 'fa fa-users', '', null);
    $m3->AddSubLink('user2','User 2', 'fa fa-users', '', null);

    // SUBMENU 2
    $m2 = new Menu();
    $m2->AddLink('settings/profil/sub2','Submenu');
    $m2->AddSubLink('user','Submenu User', '', '', $m3);
    $m2->AddSubLink('name','Submenu Name', '', '', null);

    // SUBMENU 1
    $m1 = new Menu();
    $m1->AddLink('settings/profil','Submenu');
    $m1->AddSubLink('sub1','Submenu 1',null);
    $m1->AddSubLink('sub2','Submenu 2', '', '', $m2);

    // MENU
    $m = new Menu();

    // MAIN
    $m->AddLink('','Home', 'fa fa-home');

    // MENU FIRST
    // redirect /settings to /settings/profil (do submenu)
    // $m->AddLink('/settings','Settings','fa fa-cogs', '', '/settings/profil');
    $m->AddLink('settings','Settings','fa fa-cogs');
    $m->AddSubLink('profil','Profil','fa fa-user','',$m1);
    $m->AddSubLink('pass','Password', 'fa fa-key', '', null);

    // MENU SECOND
    $m->AddLink('checkout','Checkout', 'fa fa-shopping-cart'); // dont redirect url
    $m->AddSubLink('city','Checkout city', '', '', null);
    $m->AddSubLink('adres','Checkout adres', '', '', null);

    // $m->Show();
    echo $m->GetMenu();
    $m->CssStyle();

}catch(Exception $e){
    print_r($e);
}
*/
?>
