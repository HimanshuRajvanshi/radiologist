<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
// use Illuminate\Support\Facades\Auth; 
use HTML,Form,Validator,Mail,Response,Session,Auth,DB,Redirect,Image,Password,Cookie,File,View,Hash,JsValidator,Input,Storage,URL;
use App\Models\Blog; 
use App\Models\Video; 
use App\Models\Offer;
use App\User; use App\CaseTable; use App\Like; use App\Comment; use Exception;
use App\Models\Cases;
use App\Models\Achievements;



class APIController extends Controller 
{
public $successStatus = 200;

    public function login(Request $request)
    {
     try{
        $validator = Validator::make($request->all(), [
            'email'              => 'required|Email',
            'password'           => 'required|min:6',
        ]);

        if ($validator->fails()) {
             return response()->json(['message'=>'invalid validation','status'=>'error']);
        }

        $userdata = array(
            'email'     =>  $request['email'],
            'password'  =>  $request['password'],
            'IsActive'  =>  "Y"
        );

        if (Auth::attempt($userdata)) {
            $user=User::where('email',$request['email'])->first();
            $user['imagePath'] = url('public/images')."/".$user->photo;
            $path= url('public/images/')."/".$user->photo;
            return response()->json(['message'=>'user data','error'=>0,'data'=>$user,'status'=>'success','imagePath'=>$path]);
        } else {
            return response()->json(['message'=>'Credential do not match','status'=>'error']);
        }
    }

    catch(\Exception $e){
     return Response::json([
            'message' => 'There is something wrong. Please contact administrator.'.$e->getMessage(),
            'error'=> true,
        ]);
    } 
  }    



    //For save data

    public function register(Request $request)
    {
      try
        {
            if($request->UserId ==null){
                $validator = Validator::make($request->all(), [
                    'name'           =>'required',
                    'email'          =>'required|email|unique:users',
                    'password'       =>'required|min:6',
                    'c_password'     =>'required|same:password',
                    'role'           =>'required',
                    'qualification'  =>'required|string',
                    'specially'      =>'required|string',
                    'phone'          =>'required|min:10',
                    'country'        =>'required|string',
                    'address'        =>'required',
                    'pinCode'        =>'required',
                    'photo'          =>'nullable|mimes:jpeg,bmp,png,jpg',
                ]);     
            }else{
                $validator = Validator::make($request->all(), [
                    'name'           =>'required',
                    'qualification'  =>'required|string',
                    'specially'      =>'required|string',
                    'phone'          =>'required|min:10',
                    'country'        =>'required|string',
                    'address'        =>'required',
                    'pinCode'        =>'required',
                    'photo'          =>'nullable|mimes:jpeg,bmp,png,jpg',
                ]);
            }

            if ($validator->fails()) {
               return response()->json(['message'=>$validator->errors(),'status'=>'error']);
            }

            if($request->photo == ''){
                $path='';
                $imageName='';
            }else{
               // $path = $request->file('photo')->store('photo');    
               $image = $request->file('photo'); 
               $imageName = time().'.'.$image->getClientOriginalExtension();
               $destinationPath = public_path('/images');
               $image->move($destinationPath, $imageName);    
            }

            if($request->UserId ==null){
                $user = new User();
                $user->email                = $request->email;
                $user->password             = bcrypt($request['password']);
                $user->role                 = $request->role;
                $user->name                 = $request->name;
                $user->qualification        = $request->qualification;
                $user->specially            = $request->specially;
                $user->phone                = $request->phone;
                $user->country              = $request->country;
                $user->address              = $request->address;
                $user->pinCode              = $request->pinCode;
                $user->presentWorkingPlace  = $request->presentWorkingPlace;
                $user->achievement          = $request->achievement;
                $user->publication          = $request->publication;
                $user->jobChange            = $request->jobChange;
                $user->resumeFile           = $request->resumeFile;
                $user->photo                = $imageName;
                $user->IsActive             = 'Y';
                $user->IsDeleted            = 'N';
                $user->CreatedAt            =date("Y-m-d");
                $user->save();

                $response="New user Created";   
            }else{
                User::where('UserId', $request->UserId)->update([
                        'name'                 => $request->name,
                        'qualification'        => $request->qualification,
                        'specially'            => $request->specially,
                        'phone'                => $request->phone,
                        'country'              => $request->country,
                        'address'              => $request->address,
                        'pinCode'              => $request->pinCode,
                        'presentWorkingPlace'  => $request->presentWorkingPlace,
                        'achievement'          => $request->achievement,
                        'publication'          => $request->publication,
                        'jobChange'            => $request->jobChange,
                        'resumeFile'           => $request->resumeFile,
                        'ModifiedAt'           =>date("Y-m-d"),
                    ]);
                $response ="User updated successfull.";
            }
            return response()->json(['message'=>$response,'status'=>"success"]);
    }
    catch(\Exception $e){
     return Response::json([
            'message' => 'There is something wrong. Please contact administrator.'.$e->getMessage(),
            'error'=> true,
        ]);
    }
  }

    //save and update Case
    public function postCase(Request $request)
    {
      try{
        $validator = Validator::make($request->all(), [
            'UserId'             =>'required',
            'radiologistName'    =>'required|string|max:255',
            'designation'        =>'required|string|max:255',
            'caseTittle'         =>'required|string|max:255',
            'chiefComplain'      =>'required|string|max:255',
            'photoAlbum'         =>'nullable|mimes:jpeg,bmp,png,jpg',
            // 'videos'             =>'nullable|mimes:mp4,mov,ogg,qt',
            'rating'             =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

            if($request->CaseId ==null){
                $case = new Cases();
                $case->CreatedAt =date("Y-m-d");
                $response ="Case added successfull.";
            }else{
                $case =Cases::find($request->CaseId);
                $case->ModifiedAt =date("Y-m-d");
                $response ="Case updated successfull.";
            }

            //for image
            // if($request->file('photoAlbum') != null) 
            // {     
            //     $images=[];
            //     foreach ($request->file('photoAlbum') as $key => $value) {
            //         // $imageName = time(). $key . '.' . $value->getClientOriginalExtension();
            //         // $value->move(storage_path('app/photo'), $imageName);
            //             $image = $request->file('photo'); 
            //             $imageName = time().'.'.$image->getClientOriginalExtension();
            //             $destinationPath = public_path('/images');
            //             $image->move($destinationPath, $imageName); 
                    
            //             $images[]=[
            //                 'id'=>$key++,
            //                 'name'=>$imageName
            //             ];
            //     }
            //  }else{
            //     $imageName[]="";
            //  }
            
            if($request->photoAlbum == ''){
                $path='';
                $imageName='';
            }else{
               // $path = $request->file('photo')->store('photo');    
               $image = $request->file('photoAlbum'); 
               $imageName = time().'.'.$image->getClientOriginalExtension();
               $destinationPath = public_path('/images');
               $image->move($destinationPath, $imageName);    
            }

            // if($request->$videoAlbum == ''){
            //     $path='';
            //     $videoName='';
            // }else{
            //    // $path = $request->file('photo')->store('photo');    
            //    $image = $request->file('$videoAlbum'); 
            //    $videoName = time().'.'.$image->getClientOriginalExtension();
            //    $destinationPath = public_path('/images');
            //    $image->move($destinationPath, $videoName);    
            // }            



            $case->UserId                =$request->UserId;
			$case->CategoryID            =$request->CategoryID;
            $case->radiologistName       =$request->radiologistName;
            $case->designation           =$request->designation;
            $case->caseTittle            =$request->caseTittle;
            $case->chiefComplain         =$request->chiefComplain;
            $case->previewInvestigation  =$request->previewInvestigation;
            $case->photoAlbum            =$imageName;
            $case->videos                ="videoName";
            $case->comments              =$request->comments;
            $case->rating                =$request->rating;
            $case->save();
         return response()->json(['success'=>$response,'status'=>'success']);
    }

    catch(\Exception $e){
     return Response::json([
            'error_message' => 'There is something wrong. Please contact administrator.'.$e->getMessage(),
            'error'=> true,
        ]);
      }

   }    


   //User Profile Photo Update Here
    public function proflePhotoUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo'    =>'required|mimes:jpeg,bmp,png,jpg',
            'UserId'   =>'required',    
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>'invalid validation','status'=>'error']);            
        }

      
        if($request->photo == ''){
            $path='';
            $imageName='';
        }else{
           $user= User::where('UserId',$request->UserId)->first();
           
            if(\File::exists(public_path('images/'.$user->photo))){
                \File::delete(public_path('images/'.$user->photo));
              }

              $image = $request->file('photo'); 
              $imageName = time().'.'.$image->getClientOriginalExtension();
              $destinationPath = public_path('/images');
              $image->move($destinationPath, $imageName);  

               User::where('UserId', $request->UserId)->update([
                        'photo'                => $imageName,
                        'ModifiedAt'           =>date("Y-m-d"),
                    ]);

            $user=User::where('UserId', $request->UserId)->first();
            $user['imagePath'] = url('public/images')."/".$user->photo;
            $path= url('public/images/')."/".$user->photo;
        }
        return response()->json(['message'=>$path,'status'=>'success']);   
    }


    //Get All Cases
    public function getCase(Request $request)
    {
        try{
            $cases=[];
            $cases=Cases::with('userCase')->get();
            if($cases){
                return response()->json(['response'=>$cases,'status'=>'success']);
            }
        }  
        catch(\Exception $e){
        return Response::json([
                'error_message' => 'There is something wrong. Please contact administrator.'.$e->getMessage(),
                'error'=> true,
            ]);
            }
    }



    public function postAchievement(Request $request)
    {
      try{
        $validator = Validator::make($request->all(), [
            'UserId'             =>'required',
            'title'              =>'required|string|max:255',
            'photoAlbum'         =>'nullable|mimes:jpeg,bmp,png,jpg',
            // 'videos'             =>'nullable|mimes:mp4,mov,ogg,qt',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

            if($request->AchievementsId ==null){
                $achievement = new Achievements();
                $achievement->CreatedAt =date("Y-m-d");
                $response ="Achievement added successfull.";
            }else{
                $achievement =Achievements::find($request->AchievementsId);
                $achievement->ModifiedAt =date("Y-m-d");
                $response ="Achievement updated successfull.";
            }

            if($request->photoAlbum == ''){
                $path='';
                $imageName='';
            }else{
               $image = $request->file('photoAlbum'); 
               $imageName = time().'.'.$image->getClientOriginalExtension();
               $destinationPath = public_path('/images');
               $image->move($destinationPath, $imageName);    
            }


            $achievement->UserId                =$request->UserId;
            $achievement->title                 =$request->title;
            $achievement->photoAlbum            =$imageName;
            $achievement->videos                ="videoName";
            $achievement->save();
         return response()->json(['success'=>$response,'status'=>'success']);
    }

    catch(\Exception $e){
     return Response::json([
            'error_message' => 'There is something wrong. Please contact administrator.'.$e->getMessage(),
            'error'=> true,
        ]);
      }

   }  
   
   
   #For Category listing
    function categoryList(){
	    $result = DB::table('category as t1')
					->select('t1.*')
					->get()->all();
		return response()->json(['success'=>$result,'status'=>'success']);
	}
	
	#For Doctor listing
	public function getDoctor(){
		$result = DB::table('users')
					->where('role','Doctor')
					->select('UserId','name','email','qualification','phone','photo')
					->get()->all();
		return response()->json(['success'=>$result,'status'=>'success']);
	}
   
	
   
   //for get all Achiemnent  
   public function getAchievement(Request $request)
   {
       try{
           $achievement=[];
           $achievement=Achievements::with('userAchievements')->get();
           if($achievement){
               return response()->json(['response'=>$achievement,'status'=>'success']);
           }
       }  
       catch(\Exception $e){
       return Response::json([
               'error_message' => 'There is something wrong. Please contact administrator.'.$e->getMessage(),
               'error'=> true,
           ]);
           }
   }  
   
   public function doLikeCase(Request $request)   {	  	    
   $validator = Validator::make($request->all(), ['user_id' =>'required|numeric','case_id'	=>'required|numeric',					
   'is_liked'	=>'required|numeric',					]);							
   if ($validator->fails()) {			
   return response()->json(['error'=>$validator->errors()], 401);  
   }				
   $caseLikeDetails = Like::where(['user_id'=>$request->user_id,'case_id'=>$request->case_id])->first();		
   if($caseLikeDetails==''){			
   Like::create($request->all());			
   $currentCaseLiked=1;		
   }else{			
   $caseLikeDetails->is_liked = $request->is_liked;			
   $caseLikeDetails->save();			
   $currentCaseLiked=$request->is_liked;		
   }					
   return response()->json(['is_liked'=>$currentCaseLiked,'status'=>'success']);   
   }    
   
   public function doCommentCase(Request $request) {	   	   
   $validator = Validator::make($request->all(), ['user_id' =>'required|numeric','case_id'	=>'required|numeric','comment'=>'required|between:2,150','is_deleted' =>'required|numeric',]);		
   if ($validator->fails()) {			
   return response()->json(['error'=>$validator->errors()], 401);            		
   }		
   if($request->id){			
   $comment = Comment::where(['id'=>$request->id])->first();		
   if($comment){				
	   if($request->is_deleted == 1 && $comment->is_deleted = 1){				
	   return response()->json(['comment'=>"No Comment Available",'status'=>'failed']);				
	   }elseif($request->is_deleted == 1){
	   $comment->is_deleted = 1;					
	   $comment->save();					
	   return response()->json(['comment'=>"Comment has been removed",'status'=>'failed']);				
	   }else{
	   $comment->comment = $request->comment;					
	   $comment->is_deleted = 0;					
	   $comment->save();					
	   return response()->json(['comment'=>"Comment has been updated",'status'=>'success']);									
	   }			
    }else{
	   return response()->json(['comment'=>"NO Comment Available",'status'=>'failed']);			
	}		
	}else{		
	Comment::create($request->all());		
	}		
	return response()->json(['comment'=>$request->comment,'status'=>'success']);  
	}   
	
	public function getCaseLikeComment(Request $request){	 
	$validator = Validator::make($request->all(),['case_id'=>'required|numeric']);		
	if ($validator->fails()) {		
	return response()->json(['error'=>$validator->errors()], 401);   
	}		
	try{	  
	$caseDetails= DB::table('cases')->where('CaseId',$request->case_id)->get();	
	$content=['caseDetails'=>$caseDetails,'comments'=>$this->getDetails($request),'likes'=>$this->getCaseLikes($request),'status'=>'success'];	   
	return response()->json($content);		
	}catch(Exception $e){
	throw $e;			 
	return Response::json(['error_message' => 'whoops! Something went wrong','status'=> 'error']);	
	}
	}   
	
	private function getDetails($request){
		$comments = Comment::join('cases',function($join){ 
		$join->on('comments.case_id', '=', 'cases.CaseId');
		})->join('users',function($join){ 
		$join->on('comments.user_id', '=', 'users.UserId');         					
		})->where('cases.CaseId',$request->case_id)->where('comments.is_deleted',0)
		->select('comments.id as comment_id','cases.CaseId as case_id','comments.comment as comment','cases.caseTittle as case_title','users.UserId as user_id','users.name as user_name','users.photo as user_photo')									
		->orderBy('comments.user_id','desc')->orderBy('comments.id','asc')->get();		
		if(count($comments)){	
			return $comments->map(function ($comment){
				return ['id'=>$comment->comment_id,
				'comment'=>$comment->comment,
				'user_id'=>$comment->user_id,
				'user_name'=>$comment->user_name,
				'user_photo'=>url('public/images')."/".$comment->user_photo,];			
				});		
		}return ["No Comment"];	
	}    
		
	private function getCaseLikes($request){	 			
		$likes = Like::join('cases',function($join){ 
		$join->on('likes.case_id', '=', 'cases.CaseId');     						
		})->join('users',function($join){         						
		$join->on('likes.user_id', '=', 'users.UserId');         					
		})->where('cases.CaseId',$request->case_id)							
		->where('likes.is_liked',1)							
		->select('likes.id as like_id','cases.CaseId as case_id','cases.caseTittle as case_title','users.UserId as user_id','users.name as user_name',			'users.photo as user_photo')->get();		
		if(count($likes)){		
		return $likes->map(function ($like){				  
		return ['id'=>$like->like_id,'user_id'=>$like->user_id,'user_name'=>$like->user_name,'user_photo'=>url('public/images')."/".$like->user_photo,		];			  
		});		
		}		
		return ["NO Like"];								   
	}	

}