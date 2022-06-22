<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Site;
use App\Models\Post;

class SiteController extends BaseController{
    public function __construct(){
        parent::__construct();
        $this->response = $this->error = array();
        $this->response['status'] = "0";
    }

    private function add_object($sites){
        $obj_first = $obj_last = [];
        $obj_last['id'] = count($sites)+1;
        $obj_last['title'] = "Last";
        $obj_last['site'] = "site";
        $obj_last['logo'] = "last_default.png";
        $obj_last['created_at'] = "";
        $obj_last['updated_at'] = "";
        $obj_last['logo_url'] =  asset('uploads/site_logo/last_default.png');
        $sites->push($obj_last);

        $obj_first['id'] = 0;
        $obj_first['title'] = "First";
        $obj_first['site'] = "site";
        $obj_first['logo'] = "first_default.png";
        $obj_first['created_at'] = "";
        $obj_first['updated_at'] = "";
        $obj_first['logo_url'] =  asset('uploads/site_logo/first_default.png');
        $sites->prepend($obj_first);
        return $sites;
    }

    public function getSites(Request $request){
        $sites = $first_object = $last_object = [];
        if(count($this->error) == 0){
            $sites = Site::all();
            if(!empty($sites)){
                foreach($sites as $key=>$site){
                    $sites[$key]['logo_url'] = asset('uploads/site_logo/'.$site->logo);
                }
                $this->response['data']['sites'] = $sites;
                //$sites = $this->add_object($sites);
                $this->response['status'] = "1";
                $this->response['data']['sites'] = $sites;
            }
        }
        else{
            $this->response['data']['error'] = $this->error;
        }
        
       $this->sendResponse($this->response);
    }

    public function getArticles(Request $request){
        $rules['site_id'] = 'required|integer';
        $message = [];
        $attributes = [];
        $validator = Validator::make($request->all(), $rules, $message, $attributes);
        if($validator->fails()){
            $errors = json_decode($validator->errors()->toJson(), true);
            if(!empty($errors)){
               foreach($errors as $k => $value) {
                 foreach($value as $k => $v) {
                    $this->error[] = $v;
                 }
               }
            }
        }
        if(count($this->error) == 0){
            $articles = Post::where('site_id',$request->site_id)->orderBy('id','desc')->limit($request->limit)->offset(($request->page - 1) * $request->limit)->get(['id','site_id','title','image','live_link','category','main_description','publish_date']);
            if(count($articles)>0){
                $this->response['status'] = "1";
                $this->response['data']['articles'] = $articles;
            }
            else{
                $this->error[] = "data not found.";
                $this->response['data']['error'] = $this->error;
            }
        }
        else{
            $this->response['data']['error'] = $this->error;
        }
        $this->sendResponse($this->response);
    }

    public function getArticleDetail(Request $request){
        $rules['article_id'] = 'required|integer';
        $message = [];
        $attributes = [];
        $validator = Validator::make($request->all(), $rules, $message, $attributes);
        if($validator->fails()){
            $errors = json_decode($validator->errors()->toJson(), true);
            if(!empty($errors)){
               foreach($errors as $k => $value) {
                 foreach($value as $k => $v) {
                    $this->error[] = $v;
                 }
               }
            }
        }
        if(count($this->error) == 0){
            $article = Post::where('id',$request->article_id)->first();
            if(!empty($article)){
                $this->response['status'] = "1";
                $this->response['data']['article'] = $article;
            }
            else{
                $this->error[] = "data not found.";
                $this->response['data']['error'] = $this->error;
            }
        }
        else{
            $this->response['data']['error'] = $this->error;
        }
        $this->sendResponse($this->response);
    }
}
