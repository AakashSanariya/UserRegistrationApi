<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * @var string
     */
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName', 'lastName', 'email', 'image', 'DOB', 'mobileNo', 'gender', 'password', 'status', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Image Name Change And Move Image To Destination Folder
     * @param $image
     * @return string
     */
    public static function ImageNameChange($image){
        $originalName = $image['image'];
        $imageExt = $originalName->getClientOriginalExtension();

        /* Size Check Of Image*/
        if($_FILES['image']['size'] > 50000){
            echo "File Size Is Too Large";
        }

        /* Image Extension Check*/
        if($imageExt != "jpg" && $imageExt != "jpeg" && $imageExt != "png"){
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        $date = date("m-d-Y H:i:s");
        $newImageName = $date.'_'.$originalName->getClientOriginalName();
        $directory = "user_image/";
        $imageStore = $originalName->move($directory,$newImageName);
        return $newImageName;
    }

    /*
     * For User Registration
     * */
    /**
     * @param $request
     * @return mixed
     */
    public static function register($request){
        if($request->file('image')){
            $newImageName = self::ImageNameChange($request);
            $imageData = [
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'image' => 'user_image/'.$newImageName,
                'DOB' => $request->DOB,
                'mobileNo' => $request->mobileNo,
                'gender' => $request->gender,
                'password' => md5($request->password),
                'role' => $request->role,
                'status' => $request->status,
            ];
            $register = User::create($imageData);
            return $request;
        }
    }

    public static function getLogin($request){
        $userPassword = md5($request->password);
        $userCheck = User::Select()
            ->where('email', $request->email)
            ->where('password', $userPassword)
            ->first();
        if($userCheck != null){
            $token = $userCheck->createToken('Create_Token')->accessToken;
            $data = [
                'token' => $token,
                'userId' => $userCheck->id,
                'userName' => $userCheck->firstName,
                'role' => $userCheck->role,
            ];
            return $data;
        }
        else{
            return null;
        }
    }

    public static function getUser(){
        $userDetails = User::get();
        return $userDetails;
    }
    
    public static function findUser($id){
        $userDetails = User::find($id);
        return $userDetails;
    }

    public static function userByRole($subAdmin){
        $userDetails = User::select()->where('role', $subAdmin);
        $userDetails = DataTables::of($userDetails)->make(true);
        return $userDetails;
    }
    
    public static function updateDetails($request, $id) {
        $oldDetails = User::find($id);
        $oldImage = $oldDetails['image'];
        if(isset($request['password'])){
            $request['password'] = md5($request['password']);
        }
        if(isset($request['image'])){
            if($oldDetails['image'] != null & $oldDetails['image'] != ""){
                unlink($oldDetails['image']);
            }
            $newImageName = self::ImageNameChange($request);
            $data = [
              'image' => 'user_image/'.$newImageName,
            ];
            $request = array_merge($request, $data);
//            $users =User::where('id', $id)->update();
        }
        else{
            $data = [
                'image' => null,
            ];
            $request = array_merge($request, $data);
        }
        /*
            Remove All Null Elements In Array
        */
        foreach ($request as $key => $result){
            if(is_null($result) || $result == ""){
                unset($request[$key]);
                unset($request['image']);
            }
        }
        $updateDetails = User::where('id', $id)
            ->update($request);
        return $updateDetails;
    }

    public static function userDelete($id){
        $userImage = User::find($id);
        if($userImage == null || $userImage == ""){
            return null;
        }
        unlink($userImage['image']);
        return User::where('id', $id)
            ->delete();
    }
}
