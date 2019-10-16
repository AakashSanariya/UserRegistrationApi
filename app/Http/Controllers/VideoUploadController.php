<?php

namespace App\Http\Controllers;

use App\VideoUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Prophecy\Exception\Doubler\MethodNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class VideoUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $allVideo = VideoUpload::getallVideo();
            return $this->success(['videoList' => $allVideo], 'VIDEO_LIST_SUCCESS', 200);
        }
        catch(MethodNotAllowedException $e){
            return $this.$this->error($e->getMessage());
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
        $validation = config('user_validation.Video_Validation');
        $validations = Validator::make($request->all(), $validation);
        if($validations->fails()){
            return $this->ValidationError($validations);
        }
        try{
            $videoUpload = VideoUpload::videoUpload($request);
            if ($videoUpload == false){
                return $this->error('!Opps Some Error Occurs');
            }
            return $this->success(null, 'VIDEO_INSERT_SUCCESS', 200);
        }
        catch(MethodNotFoundException $e){
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
        //
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
    public function update(Request $request, $id)
    {
        $validation = config('user_validation.Video_Update_Validation');
        $validations = Validator::make($request->all(), $validation);
        if($validations->fails()){
            return $this->ValidationError($validations);
        }
        try{
            $videoDetails = VideoUpload::updateVideoDetails($request->all(), $id);
            return $this->success(null, 'UPDATE_VIDEO_DETAILS_SUCCESS', 200);
        }
        catch(MethodNotFoundException $e){
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
            $deleteduser = VideoUpload::deleteVideo($id);
            if($deleteduser == null && $deleteduser == ""){
                return $this->error('USER_NOT_AVAILABLE', 404);
            }
            return $this->success(null, 'USER_DELETE_SUCCESS', 200);
        }
        catch(MethodNotFoundException $e){
            return $this->error($e->getMessage());
        }
    }
}
