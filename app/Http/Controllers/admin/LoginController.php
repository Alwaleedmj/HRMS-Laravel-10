<?php

namespace App\Http\Controllers\admin;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function show_login_view(){
        return view("admin.auth.login");
    }

    public function login(LoginRequest $request){
        if(auth()->guard("admin")->attempt(['username'=>$request->input('username'),'password'=> $request->input('password')]))
        {
            return redirect()->route('admin.dashboard')->with('success', "تم تسجيل دخول المستخدم".$request->input("username"));
        } else {
            return redirect()->route('admin.showLogin')->with(['error'=>'بيانات التسجيل غير صحيحه']);
        }}

    public function logout(){
        auth()->logout();
        return redirect()->route('admin.showLogin')->with('success','تم تسجيل الخروج بنجاح');
    
    }
    }

    
