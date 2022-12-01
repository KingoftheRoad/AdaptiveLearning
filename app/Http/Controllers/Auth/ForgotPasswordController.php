<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request; 
use Mail; 
use Hash;
use Illuminate\Support\Str;
use App\Traits\ResponseFormat;
use DB; 
use Carbon\Carbon; 
use App\Models\User; 

class ForgotPasswordController extends Controller
{
    use ResponseFormat;
    
    public function showForgetPasswordForm(){
        return view('auth.forgetPassword');
    }

    public function submitForgetPasswordForm(Request $request){
        if($request->email){
            if(User::where('email',$request->email)->doesntExist()){
                return $this->sendError('Please enter registered email', 422);
            }
            $token = Str::random(64);
            DB::table('password_resets')->insert([
                'email' => $request->email, 
                'token' => $token, 
                'created_at' => Carbon::now()
            ]);
            $dataSet = [
                'token' => $token
            ];
            $sendEmail = $this->sendMails('email.forgetPassword', $dataSet, $request->email, $subject='Reset Password', [], []);
            return $this->sendResponse([], 'We have sent e-mailed your password reset link');
        }
    }

    public function showResetPasswordForm($token) {
        if(isset($token) && !empty($token)){
            if(DB::table('password_resets')->where('token',$token)->doesntExist()){
                return redirect('forget-password')->withInput()->with('error_msg', 'Invalid token!');
            }else{
                return view('auth.forgetPasswordLink', ['token' => $token]);
            }
        }
    }

    public function submitResetPasswordForm(Request $request){
        if(DB::table('password_resets')->where('token',$request->token)->doesntExist()){
            return $this->sendError('Invalid Token', 422);
        }else{
            $updatePassword = DB::table('password_resets')->where('token',$request->token)->first();
            $user = User::where('email', $updatePassword->email)->update(['password' => Hash::make($request->password)]);
            DB::table('password_resets')->where(['email'=> $updatePassword->email])->delete();
            $redirectUrl = config()->get('app.url').'login';
            return $this->sendResponse(['redirectUrl' => $redirectUrl], 'Your password has been changed successfully.');
        }
    }
}
