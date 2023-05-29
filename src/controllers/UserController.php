<?php



namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\traits\ApiTrait;
use Illuminate\Support\Facades\Hash;
use hcolab\cms\models\CmsUser;
use hcolab\cms\models\CmsUserRole;
use hcolab\cms\models\CmsUserRolePermission;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    use ApiTrait;


    public function renderLoginPage(){
        
        return view('CMSViews::page.login');
    }

    public function renderForceChangePassword(){

        $session = session('admin');

        if(!$session){
            return view('CMSViews::page.login');
        }

        return view('CMSViews::page.force-change-password');
    }

    public function renderChangePassword(){
        return view('CMSViews::page.change-password');
    }




    public function renderRolePermissions($id , $token){

        if($token != md5($id.env('APP_KEY'))){
            return abort(404);
        }

        $role = CmsUserRole::where('id' , $id)->where('deleted',0)->first();

        if(!$role){
            return abort(404);
        }

        $menu_items = config('pages')['menu'];

        $menu = [];

        foreach($menu_items as $menu_item){

            if($menu_item['type'] == 'dropdown'){
                foreach($menu_item['children'] as $child){

                    $processed_child = process_menu_item($child);

                    if(!$processed_child){
                        continue;
                    }

                    $menu [] = [
                        'name' => $processed_child['name'],
                        'label' => $processed_child['label'],
                        'type' => $child['type']
                    ];  
                }

                continue;
            }

            $processed_item = process_menu_item($menu_item);

            if(!$processed_item){
                continue;
            }

            $menu [] = [
                'name' => $processed_item['name'],
                'label' => $processed_item['label'],
                'type' => $menu_item['type']
            ];  

           
        }

        $role_permissions = CmsUserRolePermission::where('role_id' , $role->id)->get()->keyBy('location');


        

    
        return view('CMSViews::page.role-permissions' , compact('menu' , 'role' , 'role_permissions'));
    }

    public function login(){
        
        $admin = CmsUser::where('phone',request()->input('username'))->orWhere('email', request()->input('username'))->first();
        

        

        if(!$admin || !Hash::check(request()->input('password'), $admin->password)){
            return redirect()->route('login')->with('notification', 'Invalid Credentials');
        }

        session(['admin' => $admin]);

        return redirect('/cms')->with('notification', 'Login Successfull');


        // return redirect()->intended('/cms');


    }

 
    public function logout(){
        session(['admin'=> null]);
        return redirect()->route('login')->with('notification', 'Logout Successfull');
    }


    public function forceChangePassword(){
        $Session = session('admin');

        $validator  =   Validator::make(request()->all() ,[
            'password' => 'required|same:confirm_password'
        ]);

        if($validator->fails()) {
           return redirect()->route('force-change-password')->with('notification', "Password & Confirm Password didn't match");
        }

    
        $admin = CmsUser::find($Session->id);
        $admin->password = Hash::make(request()->input('password'));
        $admin->save();

        session(['admin' => $admin]);

        return redirect()->route('dashboard')->with('notification', "Password successfully changed");
    
    }




    public function changePassword(){
        $Session = session('admin');

        $admin = CmsUser::find($Session->id);

        $validator  =   Validator::make(request()->all() ,[
            'password' => 'required|same:confirm_password',
            'current_password' => 'required'
        ]);

        if($validator->fails()) {
           return redirect()->route('change-password')->with('notification', "Password & Confirm Password didn't match");
        }


        if(!Hash::check(request()->input('current_password'), $admin->password) ){
            return redirect()->route('change-password')->with('notification', "Incorrect current password");;
        }

  
    
        
        $admin->password = Hash::make(request()->input('password'));
        $admin->save();

        session(['admin' => $admin]);

        return redirect()->route('dashboard')->with('notification', "Password successfully changed");
    
    }

    public function rolePermissions($id , $token){

        if($token != md5($id.env('APP_KEY'))){
            return abort(404);
        }

        $role = CmsUserRole::where('id' , $id)->where('deleted',0)->first();

        if(!$role){
            return abort(404);
        }

        $request = request()->all();

        $result = [];

        $actions = ["create_" , "read_" ,"update_" , "delete_" , "export_" , "import_"];
        foreach($request as $key => $value){
            $page = str_replace($actions , "" , $key);
            foreach($actions as $action){
                if(str_contains($key , $action)){
                $result[$page] [] = str_replace("_" , "" , $action);
                }
            }
        }

       

        foreach($result as $result_key => $result_value){
            $role_permission = CmsUserRolePermission::where('location' , $result_key)->where('role_id' , $role->id)->first();

            if(!$role_permission){
                $role_permission = new CmsUserRolePermission;
                $role_permission->location = $result_key;
                $role_permission->role_id = $role->id;
            }

            $role_permission->actions = $result_value;
            $role_permission->save();
        }


        return redirect()->back();

    }
    

}