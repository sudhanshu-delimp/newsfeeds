<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Feed;
use App\Models\Site;

class FeedController extends Controller
{
    public function index()
    {
        $pageHeading = "Manage Feed";
        $feeds = Feed::orderBy('id','desc')->get();
        return view('feed.index', compact('pageHeading','feeds'));
    }

    public function create()
    {
        $pageHeading = "Add Feed";
        $sites = Site::orderBy('title','asc')->get();
        return view('feed.create', compact('pageHeading','sites'));
    }

    public function store(Request $request)
    {
        $validate['url'] = 'required|unique:feeds';
        $validate['site_id'] = 'required';
        $request->validate($validate);
        $insert = array();
        $insert['site_id'] = $request->site_id;
        $insert['url'] = $request->url;
        $id = Feed::create($insert)->id;
        return redirect(route('manage_feed.index'))->with('success', 'Added successfully.');
    }

    public function edit(Request $request, $id){
        $pageHeading = "Update Feed";
        $sites = Site::orderBy('title','asc')->get();
        $feed = Feed::where('id',$id)->first();
        return view('feed.edit', compact('pageHeading','feed','sites'));
    }

    public function update(Request $request, $id){
        $validate['site_id'] = 'required';
        $validate['url'] = 'required|unique:feeds,url,'.$id;
        $request->validate($validate);
        $update = array();
        $update['site_id'] = $request->site_id;
        $update['url'] = $request->url;
        $id = Feed::where('id',$id)->update($update);
        return redirect()->back()->with('success', 'Updated successfully.');
    }

    public function destroy(Request $request, $id){
        $feed = Feed::findOrFail($id);
        $feed->delete();
        return redirect(route('manage_feed.index'))->with('success', 'Successfully deleted.');
    }
}
