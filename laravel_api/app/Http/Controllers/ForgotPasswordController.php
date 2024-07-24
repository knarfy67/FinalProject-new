<?php 
  
namespace App\Http\Controllers; 
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use DB; 
use Carbon\Carbon; 
use App\Models\Admin; 
use App\Models\Staff;
use App\Models\Student;  
use Illuminate\Support\Facades\Mail;
use Hash;
use Illuminate\Support\Str;
use App\Mail\MyTestMail;
use Illuminate\Support\Facades\Validator;
  
class ForgotPasswordController extends Controller
{
  
    public function submitForgetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $admin = Admin::where('email', $request->input('email'))->first();

        if(!$admin) {

            return response()->json(['error' => 'Email not found']);
        }


        $token = $admin->createToken('admin-token', ['actAsAdmin'])->plainTextToken;

    
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        

        
        Mail::send('mail.test-email', ['token' => $token, 'email' => $request->email], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json([
            'message' => 'We have e-mailed your password reset link!',
            'token' => $token,
            'email' => $request->email
        ], 200);
    
        // Mail::to($request->email)->send(new MyTestMail($name));
        
    }
    
    public function submitResetPasswordForm(Request $request)
      {
        
        $email = $request->query('email');
        $password = $request->query('password');
        $password_confirmation = $request->query('password_confirmation');
        $token = $request->query('token');

        
  
          $updatePassword = DB::table('password_resets')
                              ->where([
                                'email' => $email, 
                                'token' => $token
                              ])
                              ->first();
  
          if(!$updatePassword){
              return back()->withInput()->with('error', 'Invalid token!');
          }
  
          $admin = Admin::where('email', $request->email)
                      ->update(['password' => Hash::make($request->password)]);
 
          DB::table('password_resets')->where(['email'=> $request->email])->delete();
  
          return response()->json([
            'message' => 'Successfully Updatated!'
        ], 200);
      }

      public function submitForgetPasswordFormStaff(Request $request)
      {
          $request->validate([
              'email' => 'required|email',
          ]);
          
          $staff = Staff::where('email', $request->input('email'))->first();
  
          if(!$staff) {
  
              return response()->json(['error' => 'Email not found']);
          }
  
  
          $token = $staff->createToken('admin-token', ['actAsAdmin'])->plainTextToken;
  
      
          DB::table('password_resets')->insert([
              'email' => $request->email,
              'token' => $token,
              'created_at' => Carbon::now()
          ]);
  
          
  
          
          Mail::send('mail.test-email', ['token' => $token, 'email' => $request->email], function($message) use($request){
              $message->to($request->email);
              $message->subject('Reset Password');
          });
  
          return response()->json([
              'message' => 'We have e-mailed your password reset link!',
              'token' => $token,
              'email' => $request->email
          ], 200);
      
          // Mail::to($request->email)->send(new MyTestMail($name));
          
      }
      
      public function submitResetPasswordFormStudent(Request $request)
        {
          
          $email = $request->query('email');
          $password = $request->query('password');
          $password_confirmation = $request->query('password_confirmation');
          $token = $request->query('token');
  
          
    
            $updatePassword = DB::table('password_resets')
                                ->where([
                                  'email' => $email, 
                                  'token' => $token
                                ])
                                ->first();
    
            if(!$updatePassword){
                return back()->withInput()->with('error', 'Invalid token!');
            }
    
            $staff = Staff::where('email', $request->email)
                        ->update(['password' => Hash::make($request->password)]);
   
            DB::table('password_resets')->where(['email'=> $request->email])->delete();
    
            return response()->json([
              'message' => 'Successfully Updatated!'
          ], 200);
        }

        public function submitForgetPasswordFormStudent(Request $request)
      {
          $request->validate([
              'email' => 'required|email',
          ]);
          
          $student = Student::where('email', $request->input('email'))->first();
  
          if(!$student) {
  
              return response()->json(['error' => 'Email not found']);
          }
  
  
          $token = $student->createToken('admin-token', ['actAsStudent'])->plainTextToken;
  
      
          DB::table('password_resets')->insert([
              'email' => $request->email,
              'token' => $token,
              'created_at' => Carbon::now()
          ]);
  
          
  
          
          Mail::send('mail.test-email', ['token' => $token, 'email' => $request->email], function($message) use($request){
              $message->to($request->email);
              $message->subject('Reset Password');
          });
  
          return response()->json([
              'message' => 'We have e-mailed your password reset link!',
              'token' => $token,
              'email' => $request->email
          ], 200);
      
          // Mail::to($request->email)->send(new MyTestMail($name));
          
      }

      
}

