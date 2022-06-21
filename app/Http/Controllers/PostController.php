<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Post;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $pageHeading = "Manage Post";
        $posts = Post::orderBy('id','desc')->get();
        return view('post.index', compact('pageHeading','posts'));
    }

    public function getPosts(Request $request){
        $columns = array(0=>'id', 1=>'title', 2=>'created_at');
    
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
    
        $post =  Post::query();
        // if(count($search) > 0){
        //     $sh = (object)$search;
        //     if(!empty($sh->from_date) && !empty($sh->to_date) && $sh->from_date!=$sh->to_date){
        //       $driver->whereBetween('users.created_at',[$sh->from_date,$sh->to_date]);
        //     }
        //     else if(!empty($sh->from_date) && !empty($sh->to_date) && $sh->from_date == $sh->to_date){
        //       $driver->where('users.created_at', 'like', '%'.$sh->from_date.'%');
        //     }
        //     else if(!empty($sh->from_date)){
        //       $driver->where('users.created_at', '>=', $sh->from_date);
        //     }
        //     else if(!empty($sh->to_date)){
        //       $driver->where('users.created_at', '<=', $sh->to_date);
        //     }
        //     if(!empty($sh->name)){
        //       $driver->where('users.name','LIKE',"%{$sh->name}%");
        //     }
        //     if(!empty($sh->email)){
        //       $driver->where('users.email','LIKE',"%{$sh->email}%");
        //     }
        //     if(!empty($sh->phone)){
        //       $driver->where('users.phone','LIKE',"%{$sh->phone}%");
        //     }
        // }
        $post->offset($start);
        $post->limit($limit);
        $post->orderBy($order,$dir);
        $posts = $post->get();
        $post =  Post::query();
        //   if(count($search) > 0){
        //     $sh = (object)$search;
        //     if(!empty($sh->from_date) && !empty($sh->to_date) && $sh->from_date!=$sh->to_date){
        //       $driver->whereBetween('users.created_at',[$sh->from_date,$sh->to_date]);
        //     }
        //     else if(!empty($sh->from_date) && !empty($sh->to_date) && $sh->from_date == $sh->to_date){
        //       $driver->where('users.created_at', 'like', '%'.$sh->from_date.'%');
        //     }
        //     else if(!empty($sh->from_date)){
        //       $driver->where('users.created_at', '>=', $sh->from_date);
        //     }
        //     else if(!empty($sh->to_date)){
        //       $driver->where('users.created_at', '<=', $sh->to_date);
        //     }
        //     if(!empty($sh->name)){
        //       $driver->where('users.name','LIKE',"%{$sh->name}%");
        //     }
        //     if(!empty($sh->email)){
        //       $driver->where('users.email','LIKE',"%{$sh->email}%");
        //     }
        //     if(!empty($sh->phone)){
        //       $driver->where('users.phone','LIKE',"%{$sh->phone}%");
        //     }
        //   }
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
