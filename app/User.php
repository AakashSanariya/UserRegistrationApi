<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

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
        'firstName', 'lastName', 'email', 'image', 'password',
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
                'password' => md5($request->password)
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
            return $token;
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
    
    public static function updateDetails($request, $id) {
        $oldDetails = User::find($id);
        $oldImage = $oldDetails['image'];
        if(isset($request['image'])){
            unlink($oldDetails['image']);
            $newImageName = self::ImageNameChange($request);
            $data = [
              'image' => 'user_image/'.$newImageName
            ];
            $users =User::where('id', $id)->update($data);
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
