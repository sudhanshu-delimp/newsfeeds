<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Site;

class SiteController extends Controller
{

    public function index()
    {
        $pageHeading = "Manage Site";
        $sites = Site::orderBy('id','desc')->get();
        return view('site.index', compact('pageHeading','sites'));
    }

    public function create()
    {
        $pageHeading = "Add Site";
        return view('site.create', compact('pageHeading'));
    }

    public function store(Request $request)
    {
        $validate['title'] = 'required|unique:sites';
        $validate['site'] = 'required|unique:sites';
        $validate['lang'] = 'required';
        $validate['file'] = 'required|mimes:png,jpg,jpeg|max:2048';
        $request->validate($validate);
        $fileName = time().'.'.$request->file->extension();
        $request->file->move(public_path('uploads/site_logo'), $fileName);
        $insert = array();
        $insert['title'] = $request->title;
        $insert['site'] = $request->site;
        $insert['logo'] = $fileName;
        $insert['lang'] = $request->lang;
        $id = Site::create($insert)->id;
        return redirect(route('manage_site.index'))->with('success', 'Added successfully.');
    }

    public function edit(Request $request, $id){
        $pageHeading = "Update Site";
        $site = Site::where('id',$id)->first();
        return view('site.edit', compact('pageHeading','site'));
    }

    public function update(Request $request, $id){
        $validate['site'] = 'required|unique:sites,site,'.$id;
        $validate['title'] = 'required|unique:sites,title,'.$id;
        if(!empty($request->file)){
            $validate['file'] = 'required|mimes:png,jpg,jpeg|max:2048';
        }
        $validate['lang'] = 'required';
        $request->validate($validate);
        $update = array();
        $update['title'] = $request->title;
        $update['site'] = $request->site;
        $update['lang'] = $request->lang;
        if(!empty($request->file)){
            $fileName = time().'.'.$request->file->extension();
            $request->file->move(public_path('uploads/site_logo'), $fileName);
            $update['logo'] = $fileName;
        }
        $id = Site::where('id',$id)->update($update);
        return redirect()->back()->with('success', 'Updated successfully.');
    }

    public function destroy(Request $request, $id){
        $site = Site::findOrFail($id);
        $site->delete();
        return redirect(route('manage_site.index'))->with('success', 'Successfully deleted.');
    }
}
