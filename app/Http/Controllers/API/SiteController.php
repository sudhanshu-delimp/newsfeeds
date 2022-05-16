<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Site;

class SiteController extends BaseController{
    public function __construct(){
        parent::__construct();
        $this->response = $this->error = array();
        $this->response['status'] = "0";
    }

    public function getSites(Request $request){
        if(count($this->error) == 0){
            $sites = Site::all();
            if(!empty($sites)){
                foreach($sites as $key=>$site){
                    $sites[$key]['logo_url'] = asset('uploads/site_logo/'.$site->logo);
                }
                $this->response['status'] = "1";
                $this->response['data']['sites'] = $sites;
            }
        }
        else{
            $this->response['data']['error'] = langError($this->error);
        }
        
       $this->sendResponse($this->response);
    }
}
