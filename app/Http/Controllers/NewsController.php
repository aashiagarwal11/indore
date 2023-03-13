<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ## showing the news which is accepted by admin
        try {
            $news = News::where('status', 1)->get();
            if (!empty($news)) {
                return response()->json([
                    'message' => 'All News',
                    'data' => $news,
                ]);
            } else {
                return response()->json([
                    'error' => 'No News Found',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ## store form details by user side
        $id = auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'image' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $news = News::where('title', $request->title)->first();
            if (empty($news)) {

                $images = array();
                if ($files = $request->file('image')) {
                    foreach ($files as $file) {
                        $imgname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $img_full_name = $imgname . '.' . $extension;
                        $upload_path = 'public/image/';
                        $img_url = $upload_path . $img_full_name;
                        $file->move($upload_path, $img_full_name);
                        array_push($images, $img_url);
                    }
                }


                $imp_image =  implode('|', $images);

                $news = News::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'ads_id' => $request->ads_id ?? null,
                    'user_id' => $id,
                    'city_id' => $request->city_id ?? null,
                    'image' => $imp_image,
                ]);
                $exp = explode('|',  $imp_image);

                $data['title'] = $news->title;
                $data['description'] = $news->description;
                $data['image'] = $exp;
                $data['created_at'] = $news->created_at;
                $data['updated_at'] = $news->updated_at;

                return response()->json([
                    'message' => 'News Added Successfully',
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'error' => 'Duplicate Title',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        ## add city by admin and edit form from user side
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'city_id' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $news = News::where('id', $id)->first();
            if (!empty($news)) {
                $data['title'] = $request->title;
                $data['description'] = $request->description;
                $data['city_id'] = $request->city_id;
                $updatedata = $news->update($data);

                $get = DB::table('news')->select('news.id', 'news.title', 'news.description', 'cities.*')
                    ->join('cities', 'news.city_id', 'cities.id')->get();

                return response()->json([
                    'message' => 'News Updated Successfully',
                    'data' => $get,
                ]);
            } else {
                return response()->json([
                    'message' => 'Record Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function acceptDeny(Request $request)
    {
        ## request accept and deny by the admin for user form 
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'numeric', 'in:0,1'],
            'id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $getdata =  News::all();
        $id = $request->id;
        try {
            if (!empty($getdata)) {
                $eachnews = News::where('id', $id)->first();
                if (!empty($eachnews)) {
                    $updatecity = $eachnews->update([
                        'status' => $request->status,
                    ]);
                    $all = News::where('id', $id)->first();
                    if ($request->status == 1) {
                        return response()->json([
                            'message' => 'Accepted By Admin',
                            'data' => $all,
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Deny By Admin',
                            'data' => $all,
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'No News Available',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function newsViaAdmin(Request $request)
    {
        ## add news by admin
        $id = auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'city_id' => ['required', 'numeric'],
            'image' => ['required'],
            'video_url' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $news = News::where('title', $request->title)->first();
            if (empty($news)) {

                $images = array();
                if ($files = $request->file('image')) {
                    foreach ($files as $file) {
                        $imgname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $img_full_name = $imgname . '.' . $extension;
                        $upload_path = 'public/image/';
                        $img_url = $upload_path . $img_full_name;
                        $file->move($upload_path, $img_full_name);
                        array_push($images, $img_url);
                    }
                }
                $imp_image =  implode('|', $images);
                $news = News::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'ads_id' => $request->ads_id ?? null,
                    'image' => $imp_image,
                    'video_url' => $request->video_url,
                    'user_id' => $id,
                    'city_id' => $request->city_id ?? null,
                    'status' => 1,
                ]);
                $exp = explode('|',  $imp_image);
                $data['title'] = $request->title;
                $data['description'] = $request->description;
                $data['city_id'] = $request->city_id;
                $data['image'] = $exp;
                $data['video_url'] = $request->video_url;

                $get = DB::table('news')
                    ->select('news.title', 'news.description', 'news.image', 'news.video_url', 'cities.city_name', 'news.created_at', 'news.updated_at')
                    ->where('title', $request->title)
                    ->join('cities', 'news.city_id', 'cities.id')->get();

                return response()->json([
                    'message' => 'News Added Successfully By Admin',
                    'data' => $get,
                ]);
            } else {
                return response()->json([
                    'message' => 'Duplicate Title Not Allowed',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
