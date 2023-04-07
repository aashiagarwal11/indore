<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\City;
use App\Models\User;
use App\Models\Advertisment;
use App\Models\Watermark;
use Image;


class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ## showing the news which is accepted by admin
        try {
            $news = News::where('status', 1)->where('city_id', '!=', null)->orderBy('id', 'desc')->get()->toArray();
            if (!empty($news)) {
                $newarr = [];
                foreach ($news as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];
                    $new['audio'] = str_replace("public", env('APP_URL') . "public", $new['audio']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'All News',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No News Found',
                    'data' => [],
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
        ## store news form details by user side
        $id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if (!empty($id)) {
                if ($role_id != 1) {
                    $validator = Validator::make($request->all(), [
                        'title'       => ['required', 'string'],
                        'description' => ['required'],
                        'image'       => ['nullable'],
                        'image.*'     => ['mimes:jpeg,png,jpg']
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'success' => false,
                            'message' => $validator->errors()
                        ]);
                    }

                    $images = array();
                    if ($files = $request->file('image')) {
                        foreach ($files as $file) {
                            $imgname = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $img_full_name = $imgname . '.' . $extension;
                            $upload_path = 'public/image/';
                            $img_url = $upload_path . $img_full_name;

                            ## insert watermark
                            // $wimage = Watermark::first();

                            // $waterMarkUrl = $wimage->image;
                            // if (!empty($waterMarkUrl)) {
                            //     $imgFile = Image::make($file->getRealPath());
                            //     $imgFile->insert($waterMarkUrl, 'bottom-right', 5, 5, function ($font) {
                            //         $font->width(10);
                            //         $font->hright(2);
                            //     });
                            //     $imgFile->save($img_url);
                            // }


                            $file->move($upload_path, $img_full_name);

                            array_push($images, $img_url);
                        }
                    }

                    $imp_image =  implode('|', $images);

                    $news = News::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'user_id' => $id,
                        'city_id' => $request->city_id ?? null,
                        'image' => $imp_image,
                    ]);
                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                    $exp = explode('|',  $imp_image);

                    $news['image'] = ($exp[0] != "") ? $exp : [];

                    return response()->json([
                        'success' => true,
                        'message' => 'News Added Successfully',
                        'data' => $news,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Login as user',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as user',
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
        ## show specificnews by id
        try {
            // $specificnews = News::where('id', $id)->first();

            $specificnews = News::where('news.id', $id)->where('news.status', 1)
                ->select('news.id', 'news.title', 'users.name', 'cities.city_name', 'news.description', 'news.image', 'news.video_url', 'news.created_at', 'news.updated_at')
                ->join('users', 'users.id', 'news.user_id')
                ->join('cities', 'cities.id', 'news.city_id')
                ->get();
            if (!empty($specificnews)) {
                $newarr = [];
                foreach ($specificnews as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'News with particular Id',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No Record Exist',
                    'data' => [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        ## add city by admin and edit form of user side
        $auth_id = auth()->user()->id;
        try {
            if ($auth_id == 1) {
                $validator = Validator::make($request->all(), [
                    'title'       => ['required', 'string'],
                    'description' => ['required'],
                    'city_id'     => ['required', 'numeric'],
                    'image'       => ['nullable'],
                    'image.*'     => ['mimes:jpeg,png,jpg'],
                    'video_url'   => ['nullable'],
                    'audio'       => ['nullable'],
                    'audio.*'     => ['mimes:mpeg,mpga,mp3,wav,aac'],

                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors()
                    ]);
                }

                $news = News::where('id', $id)->first();
                if (!empty($news)) {
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


                    ## for audio

                    if ($audio = $request->file('audio')) {
                        $audioname = md5(rand('1000', '10000'));
                        $extension = strtolower($audio->getClientOriginalExtension());
                        $audio_full_name = $audioname . '.' . $extension;
                        $upload_path = 'public/image/';
                        $audio_url = $upload_path . $audio_full_name;
                        $audio->move($upload_path, $audio_full_name);
                        $imp_audio = str_replace("public", env('APP_URL') . "public", $audio_url);
                    }


                    // dd($imp_image);


                    $data['title'] = $request->title;
                    $data['description'] = $request->description;
                    $data['city_id'] = $request->city_id;
                    $data['image'] = $imp_image;
                    $data['video_url'] = $request->video_url ?? null;
                    $data['audio'] = $imp_audio ?? null;
                    $updatedata = $news->update($data);

                    $get = DB::table('news')->where('news.id', $id)->select('news.id', 'news.title', 'news.description', 'news.image', 'news.video_url', 'news.audio', 'cities.*')
                        ->join('cities', 'news.city_id', 'cities.id')->get();

                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);

                    $get[0]->image = explode('|', $imp_image);
                    
                    $get[0]->image = ($get[0]->image[0] != "") ? $get[0]->image : [];

                    return response()->json([
                        'success' => true,
                        'message' => 'News Updated Successfully',
                        'data' => $get,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record Not Exist',
                        'data' => [],
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
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
        try {
            $delete = News::where('id', $id)->first();
            if (!empty($delete)) {
                $getdeleterec = $delete->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Record Deleted Successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Record Not Exist',
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
        try {
            if ($id == 1) {
                $validator = Validator::make($request->all(), [
                    'title'       => ['required', 'string'],
                    'description' => ['required'],
                    'city_id'     => ['required', 'numeric'],
                    'image'       => ['nullable'],
                    'image.*'     => ['mimes:jpeg,png,jpg'],
                    'video_url'   => ['nullable'],
                    'audio'       => ['nullable'],
                    'audio.*'     => ['mimes:mpeg,mpga,mp3,wav,aac'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()]);
                }

                $city = City::where('id', $request->city_id)->first();
                if (!empty($city)) {
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



                    ## for audio
                    if ($audio = $request->file('audio')) {
                        $audioname = md5(rand('1000', '10000'));
                        $extension = strtolower($audio->getClientOriginalExtension());
                        $audio_full_name = $audioname . '.' . $extension;
                        $upload_path = 'public/image/';
                        $audio_url = $upload_path . $audio_full_name;
                        $audio->move($upload_path, $audio_full_name);
                    } else {

                        $audio_url = null;
                    }


                    $news = News::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'image' => $imp_image,
                        'video_url' => $request->video_url ?? null,
                        'audio' => ($audio_url != null) ? $audio_url : null,
                        'user_id' => $id,
                        'city_id' => $request->city_id,
                        'status' => 1,
                    ]);

                    $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);

                    $imp_audio = str_replace("public", env('APP_URL') . "public", $audio_url);
                    // dd($imp_audio);
                    $exp = explode('|', $imp_image);


                    $news['image'] = ($exp[0] != "") ? $exp : [];
                    $news['audio'] = $imp_audio;

                    return response()->json([
                        'success' => true,
                        'message' => 'News Added Successfully By Admin',
                        'data' => $news,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'City not exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function shownewsViacity(Request $request)
    {
        $city_id = $request->city_id;
        try {
            if ($city_id == null) {
                $news = News::where('status', 1)->orderBy('id', 'desc')->get()->toArray();
                $newarr = [];
                foreach ($news as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];
                    $new['audio'] = str_replace("public", env('APP_URL') . "public", $new['audio']);


                    ## random ads
                    $ads = Advertisment::all()->random(1);
                    if (!empty($ads)) {
                        $image = explode('|', $ads[0]->ads_image);
                        shuffle($image);

                        $ads[0]->ads_image = $image[0];
                        $ads[0]->ads_image = str_replace("public", env('APP_URL') . "public", $ads[0]->ads_image);
                    }

                    $new['randomimage'] = $ads[0]->ads_image;
                    array_push($newarr, $new);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'All News List',
                    'data' => $newarr,
                ]);
            } else {
                $city = City::where('id', $city_id)->first();
                if (!empty($city)) {
                    $news = News::where('city_id', $city_id)->where('status', 1)
                        ->select('news.id', 'news.title', 'users.name', 'cities.city_name', 'news.description', 'news.image', 'news.video_url', 'news.audio', 'news.created_at', 'news.updated_at')
                        ->join('users', 'users.id', 'news.user_id')
                        ->join('cities', 'cities.id', 'news.city_id')
                        ->orderBy('news.id', 'desc')
                        ->get();
                    if (!empty($news)) {
                        $newarr = [];
                        foreach ($news as $key => $new) {
                            $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                            $new['image'] = explode('|', $new['image']);
                            $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];
                            $new['audio'] = str_replace("public", env('APP_URL') . "public", $new['audio']);

                            ## random ads
                            $ads = Advertisment::all()->random(1);
                            if (!empty($ads)) {
                                $image = explode('|', $ads[0]->ads_image);
                                shuffle($image);

                                $ads[0]->ads_image = $image[0];
                                $ads[0]->ads_image = str_replace("public", env('APP_URL') . "public", $ads[0]->ads_image);
                            }

                            $new['randomimage'] = $ads[0]->ads_image;

                            array_push($newarr, $new);
                        }
                        return response()->json([
                            'success' => true,
                            'message' => 'All News List On The City Basis',
                            'data' => $news,
                        ]);
                    } else {
                        return response()->json([
                            'success' => true,
                            'message' => 'No News Found',
                            'data' => [],
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'City Not Exist',
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showallnewsonadmin()
    {
        ## showing the news  on admin panel 
        try {
            $news = News::orderBy('id', 'desc')->get()->toArray();
            if (!empty($news)) {
                $newarr = [];
                foreach ($news as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];
                    $new['audio'] = str_replace("public", env('APP_URL') . "public", $new['audio']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'All News',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No News Found',
                    'data' => [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function acceptNews(Request $request)
    {
        ## request accept  by the admin for user news form 
        $auth_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;

        try {
            if ($role_id == 1) {
                $newsid = $request->id;
                $validator = Validator::make($request->all(), [
                    'id' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()]);
                }


                $eachnews = News::where('id', $newsid)->first();
                if (!empty($eachnews)) {
                    $newscity = News::where('id', $newsid)->where('city_id', '!=', null)->first();

                    if (!empty($newscity)) {
                        if ($eachnews->status == 0) {
                            $eachnews->status = 1;
                            $updateStatus = $eachnews->update();
                            return response()->json([
                                'success' => true,
                                'message' => 'Request is accepted By Admin',
                            ]);
                        } elseif ($eachnews->status == 1) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Request already accepted By Admin so you can not accept again',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please add city first',
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function denyNews(Request $request)
    {
        ## request for reject  by the admin for user news form 
        $auth_id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if ($role_id == 1) {
                $newsid = $request->id;
                $validator = Validator::make($request->all(), [
                    'id' => ['required', 'numeric'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()]);
                }
                $eachnews = News::where('id', $newsid)->first();
                if (!empty($eachnews)) {
                    if ($eachnews->status == 0) {
                        $eachnews->status = 2;
                        $updateStatus = $eachnews->update();
                        return response()->json([
                            'success' => true,
                            'message' => 'Request denied By Admin',
                        ]);
                    } elseif ($eachnews->status == 2) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Request already denied By Admin so you can not deny again',
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function recentNews()
    {
        ## Recent news
        try {
            $news = News::where('status', 1)->where('city_id', '!=', null)->orderBy('id', 'desc')->limit(5)->get()->toArray();
            if (!empty($news)) {
                $newarr = [];
                foreach ($news as $key => $new) {
                    $new['image'] = str_replace("public", env('APP_URL') . "public", $new['image']);
                    $new['image'] = explode('|', $new['image']);
                    $new['image'] = ($new['image'][0] != "") ? $new['image'] : [];
                    $new['audio'] = str_replace("public", env('APP_URL') . "public", $new['audio']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'All News',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No News Found',
                    'data' => [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    // public function premiumads()
    // {
    //     ## random ads api
    //     try {
    //         $ads = Advertisment::all()->random(1);
    //         if (!empty($ads)) {
    //             $image = explode('|', $ads[0]->ads_image);
    //             shuffle($image);

    //             $ads[0]->ads_image = $image[0];
    //             $ads[0]->ads_image = str_replace("public", env('APP_URL') . "public", $ads[0]->ads_image);
    //             return response()->json([
    //                 'message' => 'Ad',
    //                 'data' => $ads,
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'message' => 'No Ads Exist',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }



    public function cityUpdate(Request $request)
    {
        $auth_id = auth()->user()->id;
        try {
            if ($auth_id == 1) {
                $validator = Validator::make($request->all(), [
                    'news_id'     => ['required', 'numeric'],
                ]);
                $news_id = $request->news_id;

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()]);
                }

                $news = News::where('id', $news_id)->first();
                if (!empty($news)) {
                    $validator = Validator::make($request->all(), [
                        'city_id'     => ['required', 'numeric'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['success' => false, 'message' => $validator->errors()]);
                    }
                    $data['city_id'] = $request->city_id;
                    $updatedata = $news->update($data);

                    // $get = DB::table('news')->where('news.id', $news_id)->select('news.id', 'news.title', 'news.description', 'news.image', 'news.video_url', 'cities.*')
                    //     ->join('cities', 'news.city_id', 'cities.id')->get();

                    $getcity = DB::table('news')->where('news.id', $news_id)->select('news.id', 'cities.*')
                        ->join('cities', 'news.city_id', 'cities.id')->get();

                    return response()->json([
                        'success' => true,
                        'message' => 'City Updated Successfully In News',
                        'data' => $getcity,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function cityUpdateAcceptStatus(Request $request)
    {
        $auth_id = auth()->user()->id;
        try {
            if ($auth_id == 1) {
                $validator = Validator::make($request->all(), [
                    'news_id'     => ['required', 'numeric'],
                ]);
                $news_id = $request->news_id;

                if ($validator->fails()) {
                    return response()->json(['success' => false, 'message' => $validator->errors()]);
                }

                $news = News::where('id', $news_id)->first();
                if (!empty($news)) {
                    $validator = Validator::make($request->all(), [
                        'city_id'     => ['required', 'numeric'],
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['success' => false, 'message' => $validator->errors()]);
                    }
                    $data['city_id'] = $request->city_id;

                    if ($news->status == 0) {
                        $data['status']  = 1;
                    }

                    $updatedata = $news->update($data);

                    $getcity = DB::table('news')->where('news.id', $news_id)->select('news.id', 'cities.*')
                        ->join('cities', 'news.city_id', 'cities.id')->get();

                    return response()->json([
                        'success' => true,
                        'message' => 'City Updated Successfully In News',
                        'data' => $getcity,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login as admin first',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
