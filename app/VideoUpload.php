<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\DataTables;

class VideoUpload extends Model
{
    protected $table = 'video_upload';

    protected $fillable = [ 'id', 'videoName', 'videoUrl' ];


    public static function videoNameChange($videoFile){
        $originalName = $videoFile['videoUrl'];
        $videoExt = $originalName->getClientOriginalExtension();
        if($videoExt != "mp4"){
            return false;
        }
        if($_FILES['videoUrl']['size'] > 2097152){
            return false;
        }

        $date = date("m-d-Y H:i:s");
        $newVideoUrl = $date.'_'.$originalName->getClientOriginalName();
        $directory = 'video';
        if(!is_dir($directory)){
            mkdir("video");
        }
        $videoStore = $originalName->move($directory, $newVideoUrl);
        return $newVideoUrl;
    }

    public static function videoUpload($param){
        if($param->file('videoUrl')){
            $newVideoName = self::videoNameChange($param);
            if($newVideoName == false){
                return false;
            }

            $videoData = [
                'videoName' => $param->videoName,
                'videoUrl' => 'video/'.$newVideoName
            ];

            $addVideo = self::create($videoData);
            return $addVideo;
        }
    }
    
    public static function getallVideo(){
        $videoDetails = self::get();
        $videoDetails = DataTables::of($videoDetails)->make(true);
        return $videoDetails;
    }
    
    public static function deleteVideo($id){
        $video = self::find($id);
        if($video == null || $video == ""){
            return null;
        }
        unlink($video['videoUrl']);
        return self::where('id', $id)
            ->delete();
    }

    public static function updateVideoDetails($request, $id){
        $oldDetails = self::find($id);
        if(isset($request['videoUrl'])){
            if($oldDetails['videoUrl'] != null & $oldDetails['videoUrl'] != ""){
                unlink($oldDetails['videoUrl']);
            }
            $newVideoName = self::videoNameChange($request);
            $data = [
              'videoUrl' => 'video/'.$newVideoName
            ];
            $request = array_merge($request, $data);
        }
        else{
            $data = [
                'videoUrl' => $oldDetails['videoUrl']
            ];
            $request = array_merge($request, $data);
        }
        $updateDetails = self::where('id', $id)
            ->update($request);
        return $updateDetails;
    }
}
