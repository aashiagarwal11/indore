<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\KrishiMandiBhav;
use App\Models\City;
use Illuminate\Support\Facades\Validator;


class CityController extends Controller
{
    public function cityList()
    {
        $apiurl = env('APP_URL') . 'api/city';
        $response = Http::get($apiurl, [
            'role_id' => auth()->user()->role_id,
        ]);
        $newdata =  $response->json($key = null, $default = null);
        $birthdayData = $newdata['data'];
        return view('admin.City.index', compact('birthdayData'));
    }

    public function getcityForm()
    {
        return view('admin.City.cityForm');
    }

    public function addcity(Request $request)
    {
        $validateImageData = $request->validate([
            'city_name'     => ['required']
        ]);

        $chkcityname = City::where('city_name', $request->city_name)->first();

        if (empty($chkcityname)) {
            $city = City::create([
                'city_name' => $request->city_name,
            ]);
            return redirect()->route('cityList')->with('message', 'Added Successfully');
        } else {
            return redirect()->route('getcityForm')->with('message', 'City Already Exist');
        }
    }

    public function getcityEditForm(Request $request, $id)
    {
        $bdata = City::where('id', $id)->first();

        return view('admin.City.cityEditForm', compact('bdata'));
    }

    public function updatecity(Request $request)
    {
        $id = $request->id;

        $city = City::where('id', $id)->first();
        if (!empty($city)) {
            $validateImageData = $request->validate([
                'city_name'       => ['required'],
            ]);
            $chkcityname = City::where('city_name', $request->city_name)->where('id', '!=', $id)->first();

            if ($chkcityname) {
                return redirect()->back()->with('message', 'City Already Exist');
            } else {
                $data['city_name'] = $request->city_name;
                $updatedata = $city->update($data);
                return redirect()->route('cityList')->with('message', 'Updated Successfully');
            }
        }
    }

    public function deletecity($id)
    {
        $del = City::where('id', $id)->first();
        $del->delete();
        return redirect()->route('cityList')->with('message', 'Deleted Successfully');

    }
}
