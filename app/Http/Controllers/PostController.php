<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Post;
use App\Models\Site;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $pageHeading = "Manage Post";
        $sites = Site::orderBy('title','asc')->get();
        return view('post.index', compact('pageHeading','sites'));
    }

    public function getSearchableFields($request){
        $search = [];
        $searchableFields = ['title','created_at','site','from_date','to_date'];
        foreach($searchableFields as $field){
            if(isset($request[$field]) && trim($request[$field])!=""){
            $search[$field] = $request[$field];
            }
        }
        return $search;
    }

    public function getPosts(Request $request){
        $search = $this->getSearchableFields($request->all());
        $columns = array(0=>'posts.id', 1=>'posts.site.title', 2=>'posts.title', 3=>'posts.created_at');

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $post =  Post::query();
        if(count($search) > 0){
            $sh = (object)$search;
            if(!empty($sh->from_date) && !empty($sh->to_date) && $sh->from_date!=$sh->to_date){
                $post->whereBetween('posts.created_at',[$sh->from_date.' 00:00:00',$sh->to_date.' 23:59:00']);
            }
            else if(!empty($sh->from_date) && !empty($sh->to_date) && $sh->from_date == $sh->to_date){
                $post->where('posts.created_at', 'like', '%'.$sh->from_date.'%');
            }
            else if(!empty($sh->from_date)){
                $post->where('posts.created_at', '>=', $sh->from_date);
            }
            else if(!empty($sh->to_date)){
                $post->where('posts.created_at', '<=', $sh->to_date);
            }
            if(!empty($sh->title)){
                $post->where('posts.title','LIKE',"%{$sh->title}%");
            }
            if(!empty($sh->site)){
                $post->where('posts.site_id',$sh->site);
            }
        }
        $post->offset($start);
        $post->limit($limit);
        $post->orderBy($order,$dir);
        $posts = $post->get();
        $post =  Post::query();
          if(count($search) > 0){
            $sh = (object)$search;
            if(!empty($sh->from_date) && !empty($sh->to_date) && $sh->from_date!=$sh->to_date){
              $post->whereBetween('posts.created_at',[$sh->from_date.' 00:00:00',$sh->to_date.' 23:59:00']);
            }
            else if(!empty($sh->from_date) && !empty($sh->to_date) && $sh->from_date == $sh->to_date){
              $post->where('posts.created_at', 'like', '%'.$sh->from_date.'%');
            }
            else if(!empty($sh->from_date)){
              $post->where('posts.created_at', '>=', $sh->from_date);
            }
            else if(!empty($sh->to_date)){
              $post->where('posts.created_at', '<=', $sh->to_date);
            }
            if(!empty($sh->title)){
              $post->where('posts.title','LIKE',"%{$sh->title}%");
            }
            if(!empty($sh->site)){
              $post->where('posts.site_id',$sh->site);
            }
          }
          $totalData  = $post->count();
          $totalFiltered = $totalData;
        $data = array();
        if(!empty($posts)){
          foreach ($posts as $key=>$post){
            $nestedData['sn'] = ($start+($key+1));
            $nestedData['site'] = $post->site->title;
            $nestedData['title'] = $post->title;
            $nestedData['created'] = \Carbon\Carbon::parse($post->created_at)->format('Y-m-d h:i A');
            $nestedData['live_link'] = '<a target="_blank" href="'.$post->live_link.'">View</a>';
            $data[] = $nestedData;
          }
        }

        $json_data = array(
        "draw"            => intval($request->input('draw')),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data
        );
        echo json_encode($json_data);
      }
}
