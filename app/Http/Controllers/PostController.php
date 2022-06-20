<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Post;

class PostController extends Controller
{

    public function index()
    {
        $pageHeading = "Manage Post";
        $posts = Post::orderBy('id','desc')->get();
        return view('post.index', compact('pageHeading','posts'));
    }

}
