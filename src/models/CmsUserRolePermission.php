<?php

namespace hcolab\cms\models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsUserRolePermission extends Model
{
    use HasFactory;

    protected $casts = [
        'actions' => 'array'
    ];


    public static function getPermissions($entity){

        // dd($entity);
        
        $admin = request()->admin;


       $role =  self::where('location' , $entity)->where('role_id' , $admin->role_id)->first();

       if(!$role){
            return [];
       }

       return $role->actions;

    }

    public static function checkPermissions($entity , $action){
        $actions = self::getPermissions($entity);

        return in_array($action , $actions);
    }


}
