<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisment;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Auth;



class AdminController extends Controller
{
    public function loginPage(Request $request)
    {
        return view('admin.login');
    }

    public function loginadmins(Request $request)
    {
        $validatedData = $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        if (auth()->attempt(array('email' => $request->email, 'password' => $request->password))) {
            if (auth()->user()->role_id == 1) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('loginpage');
            }
        } else {
            return redirect()->route('loginpage')
                ->with('error', 'Email-Address And Password Are Wrong.');
        }
    }

    public function index()
    {
        return view('admin.layout.dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('dashboard');
    }
}
