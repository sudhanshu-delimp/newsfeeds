<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Session;
use App\Models\User;

class UserController extends Controller
{

    public function login(Request $request)
    {
      if($request->session()->has('account')){
        return redirect(route('manage_post.index')); 
      }
      else{
        $pageHeading = "Login";
        return view('user.login', compact('pageHeading'));
      }
    }

    public function logout(Request $request)
    {
      if($request->session()->has('account')){
        $request->session()->flush();
        return redirect(route('admin_login')); 
      }
    }

    public function processLogin(Request $request){
        $error = [];
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if($validator->fails()){
            $errors = json_decode($validator->errors()->toJson(), true);
            if (!empty($errors)){
               foreach($errors as $k => $v) {
                 $error[] = $v[0];
               }
            }
        }
        else{
            $user = User::where('email','=',$request->email)->first();
            $password = $request->password;
            if(!$user){
              $error[] = 'User does not exist.';
            }
            else if(Hash::check($password, $user->password) === false){
              $error[] = 'Invalid password.';
            }
          }

          if(count($error) == 0){
            $this->response['status'] = '1';
            $request->session()->put('account', $user);
            return redirect(route('manage_post.index'))->with('success', 'Successfully Login.');
          }
          else{
            return redirect()->back()->with('errors', $error);
          }
    }

    public function index()
    {
        $pageHeading = "Login";
        return view('user.login', compact('pageHeading'));
    }

    public function create()
    {
        $pageHeading = "Add Feed";
        $sites = Site::orderBy('title','asc')->get();
        return view('feed.create', compact('pageHeading','sites'));
    }

    public function store(Request $request)
    {
        
    }

    public function edit(Request $request, $id){
       
    }

    public function update(Request $request, $id){
        
    }

    public function destroy(Request $request, $id){
        
    }
}
