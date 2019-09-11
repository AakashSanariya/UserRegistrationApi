<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Prophecy\Exception\Doubler\MethodNotFoundException;

class UserRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $userDetails = User::getUser();
            return $this->success(['userDetails' => $userDetails], 'IMAGE_LIST_SUCCESS', 200);
        }
        catch (MethodNotFoundException $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = config('user_validation.Add_User_Validation');
        $validations = Validator::make($request->all(), $validation);
        if($validations->fails()){
            return $this->ValidationError($validations);
        }
        try{
            $user = User::register($request);
            return $this->success(null, 'USER_INSERT_SUCCESS', 200);
        }
        catch (MethodNotFoundException $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $userDetails = User::findUser($id);
            if($userDetails == null){
                return $this->error('USER_DETAILS_NOT_AVAILABLE', 404);
            }
            else{
                return $this->success(['userDetails' => $userDetails], 'USER_DETAILS_SUCCESS', 200);
            }
        }
        catch (MethodNotFoundException $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDetails(Request $request, $id)
    {
        $validation = config('user_validation.Update_Validation');
        $validations = Validator::make($request->all(), $validation);
        if($validations->fails()){
            return $this->validationError($validations);
        }
        try{
            $userDetails = User::updateDetails($request->all(), $id);
            return $this->success(null, 'UPDATE_DETAILS_SUCCESS', 200);
        }
        catch (MethodNotFoundException $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $userDelete = User::userDelete($id);
            if($userDelete == null || $userDelete == ""){
                return $this->error('USER_NOT_AVAILABLE', 404);
            }
            return $this->success(null, 'USER_DELETE_SUCCESS', 200);
        }
        catch (MethodNotFoundException $e){
            return $this->error($e->getMessage());
        }
    }
    
    public function login(Request $request){
        $validation = config('user_validation.Login_Validation');
        $validations = Validator::make($request->all(), $validation);
        if($validations->fails()){
            return $this->validationError($validations);
        }
        try{
            $loginUser = User::getLogin($request);
            if($loginUser != null){
                return $this->success(['data' => $loginUser], 'USER_LOGGIN_SUCCESS', 200);
            }
            else{
                return $this->error('Credentials Not Matched', 401);
            }
        }
        catch (MethodNotFoundException $e){
            return $this->error($e->getMessage());
        }
    }
}
