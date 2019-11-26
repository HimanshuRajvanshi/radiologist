<?php

namespace App\Http\Controllers;

use Hash, Auth, DB, Validator;
use App\Model\User;
use Illuminate\Http\Request;
use App\Model\user_details;
use App\Model\UserProfileSetting;


class ApiAccountController extends Controller
{
    protected function check_required($array){
        $requried = "";
        foreach($array as $key => $value) {
            if ($value==''){
                $requried .= $key.',';
            }
        }
        if($requried!=""){
            return rtrim($requried,',');
        }else{
            return false;
        }
    }

    protected function response($arr){
        return response($arr,$arr['status'])->header('Content-Type', 'application/json');
    }

    protected function getToken(){
        $token   =  md5(uniqid(rand().microtime(), true));
        return $token;
    }

    protected function upload_multiple($request,$file,$path){
        if($request->file($file)){
            $files = $request->file($file);
            foreach($files as $file){
                $name = md5(md5(time()).md5(rand(12345,99999))).'.'.$file->extension();
                $picture = $file->move($path,$name);
                $final[] = $path.'/'.$name;
            }
            return $final;
        }else{
            return [];
        }
    }

    public function loginClient(Request $request) {
        $input = $request->all();
        $data  = ['email'=>@$input['email'],'password'=>@$input['password'],'device_token'=>@$input['device_token'],'device_type'=>@$input['device_type']];
        if($this->check_required($data)){
            $response = ['status'=>400,"message"=>"Missing fields: ".$this->check_required($data)];
            return $this->response($response);
        }else{
            $user = User::where(['email'=>$data['email']])->first();
            $isvalid = Hash::check($data['password'], $user->password);
            if($isvalid){
                // set device_type and device_type
                $user->device_token = $data['device_token'];
                $user->device_type  = $data['device_type'];
                // set session for this Login
                $user->sessionkey = $this->getToken();
                $user->save();
                return $this->response([ 'status'=>200,"message"=>"Login Success", "data"=>$user,"success"=>1 ]);           
            }else{
                return $this->response([ 'status'=>401,"message"=>"Login Faild","success"=>0 ]);
            }
        }
    }

    public function signupClient(Request $request) {
       
        $input = $request->all();
        $data  = [  'name'=>@$input['name'],'email'=>@$input['email'],'password'=>@$input['password'],'phone'=>@$input['phone'],'location'=>@$input['location'],'birth_date'=>@$input['birth_date'],'birth_month'=>@$input['birth_month'],'birth_year'=>@$input['birth_year'],'gender'=>@$input['gender'],'four_digit_pin'=>@$input['four_digit_pin'],'role'=>@$input['role'],'profile_img_path'=>@$input['profile_img_path'],'device_token'=>@$input['device_token'],'device_type'=>@$input['device_type']    ];           
        
        if($this->check_required($data)){
            $response = ['status'=>400,"message"=>"Missing fields: ".$this->check_required($data)];
            return $this->response($response);
        }else{
            $user = User::where(['email'=>$data['email']])->first();
            if($user!=null){
                return $this->response([ 'status'=>409,"message"=>"Email Id already exists","success"=>0 ]);    
            }
            $data['role_text']  = $data['role']==1 ? 'Seller' : 'Buyer' ;
            $data['password']   = Hash::make($data['password']);
            $data['sessionkey'] = $this->getToken();
            
            $dataForUserDetails['birth_date']  = $data['birth_date'];
            $dataForUserDetails['birth_month'] = $data['birth_month'];
            $dataForUserDetails['birth_year']  = $data['birth_year'];

            unset($data['birth_date'],$data['birth_month'],$data['birth_year']);

            if(User::insert( $data )){
                $user = User::where(['email'=>$data['email']])->first();
                $dataForUserDetails['ref_id'] = $user->id;
                // insert data for `user_details` table
                if(user_details::insert( $dataForUserDetails )){
                    if(isset($input['images']) && $input['images'] ){
                        $arrImages = explode(",",$input['images']);
                        foreach( $arrImages as $img ){
                            DB::table('seller_pictures_for_sell_app')->insert(
                                [
                                    'user_id'        =>  $user->id,
                                    'picture_path'   =>  $img
                                ]
                            );
                        }
                    }
                    return $this->response([ 'status'=>200,"message"=>"Signup Success", "data"=>$user,"success"=>1 ]);
                }
            }
            return $this->response([ 'status'=>401,"message"=>"Signup Faild","success"=>0 ]);
        }
    }
    
    
    // check socialId exists or not
    public function isSocialIdExists(Request $request){
        $input    =  $request->all();
        $data  = [ 'socialId'=>@$input['socialId'],'socialAccountType'=>@$input['socialAccountType'] ];           
        
        if($this->check_required($data)){
            $response = ['status'=>400,"message"=>"Missing fields: ".$this->check_required($data)];
            return $this->response($response);
        }else{
            // check this socialId exists or not
            $user = User::where([ 'socialId'=>$data['socialId'], 'socialAccountType'=>$data['socialAccountType'] ])->first();
            if($user!=null){
                return $this->response([ 'status'=>200,"message"=>"socialId Exists","success"=>1 ]);
            }
            return $this->response([ 'status'=>200,"message"=>"socialId not Exists","success"=>1 ]);
        }    

    }


    public function uploadFiles(Request $request){
        if( $request->file('files') != null ){
            $data = $this->upload_multiple($request,'files','public/Uploads/user-profile-pics');
            $counter = 0;
            foreach($data as $data2){
                $data[$counter] = substr($data2,7);
                $counter++;
            } 
            return $this->response([ 'status'=>200,"message"=>"Files Uploaded", "data"=>$data,"success"=>1 ]);
                  
        }
        return $this->response([ 'status'=>400,"message"=>"Files Upload Failed","success"=>0 ]);    
    }


    public function socialLogin(Request $request){
        // check user is already signUp or not , {{ if not }} signup user else login the user
        
        $input    =  $request->all();

        if( !isset($input['signUp']) && trim(@$input['signUp'])=='' ){
            $response = ['status'=>400,"message"=>"Missing field : signUp "];
            return $this->response($response);
        }

        if( $input['signUp']==1 ){
            // sign up the user
            $data  = [  'name'=>@$input['name'],'phone'=>@$input['phone'],'location'=>@$input['location'],'birth_date'=>@$input['birth_date'],'birth_month'=>@$input['birth_month'],'birth_year'=>@$input['birth_year'],'gender'=>@$input['gender'],'four_digit_pin'=>@$input['four_digit_pin'],'role'=>@$input['role'],'profile_img_path'=>@$input['profile_img_path'],'device_token'=>@$input['device_token'],'device_type'=>@$input['device_type'],'socialId'=>@$input['socialId'], 'socialAccountType'=>@$input['socialAccountType']   ];           
        
            if($this->check_required($data)){
                $response = ['status'=>400,"message"=>"Missing fields: ".$this->check_required($data)];
                return $this->response($response);
            }else{
                $data['role_text']  = $data['role']==1 ? 'Seller' : 'Buyer' ;
                $data['sessionkey'] = $this->getToken();
                
                $dataForUserDetails['birth_date']  = $data['birth_date'];
                $dataForUserDetails['birth_month'] = $data['birth_month'];
                $dataForUserDetails['birth_year']  = $data['birth_year'];
    
                unset($data['birth_date'],$data['birth_month'],$data['birth_year']);
    
                if(User::insert( $data )){
                    $user = User::where(['socialId'=>$data['socialId']])->first();
                    $dataForUserDetails['ref_id'] = $user->id;
                    // insert data for `user_details` table
                    if(user_details::insert( $dataForUserDetails )){
                        if(isset($input['images']) && $input['images'] ){
                            $arrImages = explode(",",$input['images']);
                            foreach( $arrImages as $img ){
                                DB::table('seller_pictures_for_sell_app')->insert(
                                    [
                                        'user_id'        =>  $user->id,
                                        'picture_path'   =>  $img
                                    ]
                                );
                            }
                        }
                        return $this->response([ 'status'=>200,"message"=>"Login Success", "data"=>$user,"success"=>1 ]);
                    }
                }
                return $this->response([ 'status'=>401,"message"=>"Login Failed","success"=>0 ]);
            }
        }else{
            // already signed up , set sessionkey and login the user
            $data  = [ 'socialId'=>@$input['socialId'],'device_token'=>@$input['device_token'],'device_type'=>@$input['device_type'] ];           
        
            if($this->check_required($data)){
                $response = ['status'=>400,"message"=>"Missing fields: ".$this->check_required($data)];
                return $this->response($response);
            }else{
                $user = User::where(['socialId'=>$data['socialId']])->first();
                if($user!=null){
                    // set device_type and device_type
                    $user->device_token = $data['device_token'];
                    $user->device_type  = $data['device_type'];
                    // set session for this Login
                    $user->sessionkey = $this->getToken();
                    $user->save();
                    return $this->response([ 'status'=>200,"message"=>"Login Success", "data"=>$user,"success"=>1 ]);           
                }else{
                    return $this->response([ 'status'=>401,"message"=>"Login Failed","success"=>0 ]);
                }

            }    

        }
      

    }


    public function forgotPassword(Request $request){
        $input = $request->all();
        $data  = [ 'email'=>@$input['email'] ];           
        
        if($this->check_required($data)){
            $response = ['status'=>400,"message"=>"Missing fields: ".$this->check_required($data)];
            return $this->response($response);
        }else{
            $userEmail = $data['email'];
            $user = User::where([ 'email'=>$userEmail ])->first();
            if($user==null){
                return $this->response([ 'status'=>401,"message"=>"No such Email Id exists","success"=>0 ]);
            }

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://nladultcams.com/password/email",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n$userEmail\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
              CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Authorization: Bearer ghdytuy7s6$%$%^&dfsbguey67Ft567er4tysb76tbvsrtyvuesr7htd",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Cookie: XSRF-TOKEN=eyJpdiI6Ikd3OE9ndkNVcSttc3dSQloySlV1Q2c9PSIsInZhbHVlIjoiUkdQeTF0enRyWUpOWVVUSmZIdzRHY2wwWng1a1ZyYmtzb3QyV05XUHJUWENkd1hld1J1cWVWSEkxazR1dXRTdyIsIm1hYyI6IjgzN2FjYzhiZjRhNDJhNzRjMzRmM2I4N2M0MTRlMjY1ZGQzNjMyNjIzNjdhNWFlZDg2MjI5ODgyMWMxNGE2ODcifQ%3D%3D; nladult_session=eyJpdiI6InJRZjA0aTZTSDNCdDlBd1ZnK2VzWHc9PSIsInZhbHVlIjoiV3ExWldQcVkzZVg1cUlCMDlzaUlCOXhIaDFoemNSOElEZllHRjlGQXdJMVkwSVJ4Z0lmcys0b08xVlYwVjNDTCIsIm1hYyI6IjRkOTQ1YzAzOWYyNjc0MDQwYjYwNjk5NGY2ODgyNjVhNWI5NmQ2YzJmMjFiM2U5YWExMTBhZjBjZWNlNmVlOGEifQ%3D%3D",
                "Postman-Token: 76ed3317-d446-4875-bc8a-cf692f94f0b3,bc6b8865-03c0-4095-9a8a-8e699c2eef32",
                "Referer: https://nladultcams.com/password/email",
                "User-Agent: PostmanRuntime/7.15.2",
                "cache-control: no-cache",
                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            if($err){
                return $this->response([ 'status'=>400,"message"=>"Email sent Failed","success"=>0 ]);
            }
            return $this->response([ 'status'=>200,"message"=>"Email sent for password reset", "success"=>1 ]);      
        }

    }



    //For Profile update 
    public function updateProfile(Request $request)
    {
      try{
        $input = $request->all();
        $data  = [  'name'=>@$input['name'],'email'=>@$input['email'],'phone'=>@$input['phone'],'location'=>@$input['location'],'birth_date'=>@$input['birth_date'],'birth_month'=>@$input['birth_month'],'birth_year'=>@$input['birth_year'],'gender'=>@$input['gender'],/*'profile_img_path'=>@$input['profile_img_path']*/];                       
        if($this->check_required($data)){
            $response = ['status'=>400,"message"=>"Missing fields: ".$this->check_required($data)];
            return $this->response($response);
        }else{
            $header=$request->header();
            $user = User::where(['id' =>$header['userid'][0]])->first();
            if($user->name != $data['name']){
                $tmp_user =User::Where(['name' =>$data['name']])->first();
                if($tmp_user!=null){
                    return $this->response([ 'status'=>409,"message"=>"username already exists","success"=>0 ]);    
                }
            }elseif($user->email != $data['email']) {
               $tmp_user = User::where(['email'=>$data['email']])->first();
                if($tmp_user!=null){
                    return $this->response([ 'status'=>409,"message"=>"Email Id already exists","success"=> 0]);    
                }
            } else {
                $user->name       =$data['name'];
                $user->email      =$data['email'];
                $user->phone      =$data['phone'];
                $user->location   =$data['location'];
                $user->gender     =$data['gender'];
                $user->updated_at =date("Y-m-d");   
                $user->save();

                $userDetails=user_details::where('ref_id',$request->userid)->first();
                $userDetails->gender        =$data['gender'];
                $userDetails->birth_date    =$data['birth_date'];
                $userDetails->birth_month   =$data['birth_month'];
                $userDetails->birth_year    =$data['birth_year'];
                $userDetails->updated_at    =date("Y-m-d");
                $userDetails->save();

                return $this->response([ 'status'=>200,"message"=>"Record successfully updated", "data"=>$user,"success"=>1 ]);
            }
        }
    }catch(\Exception $e){
        return $this->response([ 'status'=>404,"message"=>"There is something wrong".$e->getMessage(), 'error'=> true]);
      }
    }

    //For User LogOut
    public function logout(Request $request)
    {   
        $header=$request->header();
        $user = User::where(['id' =>$header['userid'][0]])->first();
        $user->sessionkey ='';
        $user->save();
        return $this->response([ 'status'=>200,"message"=>"Logout successfully","success"=>1 ]);
    }

    //For Profile setting
    public function profileSetting(Request $request)
    {
      try{
            $input = $request->all();
            $data  = [  'private_account'=>@$input['private_account'],'push_notification'=>@$input['push_notification']];           

            if($this->check_required($data)){
                $response = ['status'=>400,"message"=>"Missing fields: ".$this->check_required($data)];
                return $this->response($response);
            }

            $header=$request->header();
            $prfile_setting=UserProfileSetting::where('user_id',$header['userid'][0])->first();
            if($prfile_setting != null){
                $prfile_setting->updated_at=date("Y-m-d");
                $msg='Record successfully updated';

            }else{
                $prfile_setting= new UserProfileSetting();
                $prfile_setting->created_at=date("Y-m-d");
                $msg='Record successfully added';
            }
        
            $prfile_setting->user_id =$header['userid'][0];
            $prfile_setting->private_account    =$data['private_account'];
            $prfile_setting->push_notification  =$data['push_notification'];
            $prfile_setting->save();
            
            return $this->response([ 'status'=>200,"message"=>$msg, "data"=>$prfile_setting,"success"=>1 ]);            
   
        }catch(\Exception $e){
            return $this->response([ 'status'=>404,"message"=>"There is something wrong".$e->getMessage(), 'error'=> true]);
        }
    }

    //For all buyer and seller details
    public function buyerSellerDetails(Request $request)
    {
       try{ 
            $input = $request->all();
            $data  = [  'user_type'=>@$input['user_type']];       

            if($this->check_required($data)){
                $response = ['status'=>400,"message"=>"Missing fields: ".$this->check_required($data)];
                return $this->response($response);
            }

            $header=$request->header();
            $user_type=$request->user_type;
            $users_details=User::Select('id','name','email','phone','location','gender','bio','hobbies')->where('role_text',$user_type)->where('id' ,'!=',$header['userid'][0])->get();
        
        return $this->response([ 'status'=>200,"message"=>$user_type." all records", "data"=>$users_details,"success"=>1 ]);

        }catch(\Exception $e){
            return $this->response([ 'status'=>404,"message"=>"There is something wrong".$e->getMessage(), 'error'=> true]);
        } 
    }


    

}