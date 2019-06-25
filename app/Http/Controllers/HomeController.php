<?php
namespace App\Http\Controllers;
use HTML,Form,Validator,Mail,Response,Session,Auth,DB,Redirect,Image,Password,Cookie,File,View,Hash,JsValidator,Input,Storage,URL;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Offer;
use App\Models\Video;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

     //for show admin dashboard
    public function index()
    {
        // return view('home');
        return view('admin.dashboard');
    }

    //For Get all blog information
    public function getBlogs(Request $request)
    {
        $data = [];
        $blogs=Blog::get();
        if(isset($blogs)){
            $data["blogs"]=$blogs;

           return view('admin.blog.show_blog', $data);
        }else{
            return "No data found";
            // return view('admin.user.show_user');
        } 
    }

    //for Update Status
    public function statusUpdate(Request $request,$typ,$update,$id)
    { 
      try{
          if($typ== 'offer'){
                $model_status=Offer::where('OfferId',$id)->first();
          }elseif($typ == 'blog'){
                $model_status=Blog::where('BlogId',$id)->first();
          }elseif($typ == 'video'){
               $model_status=Video::where('VideoId',$id)->first();    
          }
            
            if($model_status){
                    if($model_status->IsActive == null){
                        $status=$update;
                    }if($model_status->IsActive == 1){
                        $status=$update;
                    }else{
                        $status=$update;
                    }

                    $model_status->IsActive       = $status;
                    $model_status->ModifiedAt     = date("Y-m-d");
                    $model_status->save();
                    return Response::json(['success_message' => 'Status updated successfully.','error'=>"0"]);
            }else{
                return Response::json(['success_message' => 'Status can not be updated','error'=>"1"]);
            }
        }
        catch(\Exception $e){
            return Response::json([
                'error_message' => 'There is something wrong. Please contact administrator.'.$e->getMessage(),
                'error'=> true,
            ]);
        }
    }//end statusBlogUpdate


    //For get all offer data
    public function getoffers(Request $request)
    {
        $data=[];
        $offers=Offer::get();
        if(isset($offers)){
            $data["offers"]=$offers;
             return view('admin.offer.show_offer', $data);
        }else{
            return "No data found";
            // return view('admin.user.show_user');
        } 
    }

    //For get all offer data
    public function getVideos(Request $request)
    {
        $data=[];
        $videos=Video::get();
        if(isset($videos)){
            $data["videos"]=$videos;
           return view('admin.video.show_video', $data);
        }else{
            return "No data found";
            // return view('admin.user.show_user');
        } 
    }


    
}
