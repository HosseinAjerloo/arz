<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\SearchRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $currentUser=Auth::user();
        $users=User::orderBy('id','desc')->where('id','!=',$currentUser->id)->paginate(15,['*'],'pageUser');
        return view('Admin.User.index',compact('users','currentUser'));
    }
    public function inactive(User $user)
    {
        $result=$user->update(['is_active'=>$user->is_active==1?0:1]);
        return $result?redirect()->route('panel.admin.user.index')->with('success','اطلاعات کاربر بروز رسانی شد'):route('panel.admin.user.index')->with('error','اطلاعات کاربر بروز رسانی نشد لطفا چند دقیه دیگه دوباره تلاش کنید.');
    }
    public function search(SearchRequest $request)
    {
        $inputs=$request->all();
        $users=User::Search($inputs)->get();
        return view('Admin.User.search',compact('users'));
    }
}
