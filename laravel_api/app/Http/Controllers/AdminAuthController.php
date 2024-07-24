<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

//use App\Http\Controllers\Storage;

class AdminAuthController extends Controller
{  
       
public function register(Request $request)
{
    $rules = [
        'id_number' => 'required|string|max:255',
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'email' => 'required|email|unique:admins|max:255',
        'password' => 'required|string|min:8',
    ];

    // Custom error messages
    $messages = [
        'id_number.required' => 'id_number is required.',
        'fname.required' => 'first name is required.',
        'lname.required' => 'last name is required.',
        'email.required' => 'Email is required.',
        'email.email' => 'Invalid email format.',
        'email.unique' => 'Email already exists.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters long.',
    ];

    
    $validator = Validator::make($request->all(), $rules, $messages);

    
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    try {
        $admin = Admin::create([
            'id_number' => $request->input('id_number'),
            'fname' => $request->input('fname'),
            'lname' => $request->input('lname'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        
        $token = $admin->createToken('admin-token', ['actAsAdmin'])->plainTextToken;
        return response()->json(['message' => 'Admin created successfully', 'data' => $admin], 201);
        

       // return response()->json(['token' => $token, 'admin' => $admin], 201);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create admin', 'message' => $e->getMessage()], 500);
    }
   
}


public function login(Request $request)
{
    $admin = Admin::where('email', $request->input('email'))->first();

    if (!$admin || !Hash::check($request->input('password'), $admin->password)) {
        return response()->json(['message' => 'Email or password incorrect'], 401);
        
    }
    $token = $admin->createToken('admin-token', ['actAsAdmin'])->plainTextToken;
    return response()->json(['token' => $token, 'admin' => $admin], 200);
}


public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully'], 200);
}

public function addStaff(Request $request)
{
    $rules = [
        'id_number' => 'required|string|max:255',
        'staff' => 'required|string|max:255',
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'email' => 'required|email|unique:admins|max:255',
        'password' => 'required|string|min:8',
        'file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'imageURL' => 'required|string', 
    ];

    $messages = [
        'id_number.required' => 'id number is required.',
        'staff.required' => 'select staff is required.',
        'fname.required' => 'first name is required.',
        'lname.required' => 'last name is required.',
        'email.required' => 'Email is required.',
        'email.email' => 'Invalid email format.',
        'email.unique' => 'Email already exists.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters long.',
        'file.required' => 'Image is required',
        'imageURL.required' => 'Signature is required',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    try {
        // Save signature image
        $signatureDataUrl = $request->input('imageURL');
        $signatureData = explode(',', $signatureDataUrl)[1];
        $imageData = base64_decode($signatureData);
        $signatureFilename = 'signature_' . uniqid() . '.png';
        File::put(storage_path('app/pictures/' . $signatureFilename), $imageData); // Save file using File facade


        $staff = Staff::create([
            'id_number' => $request->input('id_number'),
            'staff' => $request->input('staff'),
            'fname' => $request->input('fname'),
            'lname' => $request->input('lname'),
            'email' => $request->input('email'),
            'file' => $request->file('file')->store('pictures'),
            'imageURL' => $signatureFilename, // Store filename instead of storing in storage directory
            'password' => bcrypt($request->input('password')),
        ]);

        $token = $staff->createToken('staff-token', ['actAsStaff'])->plainTextToken;

        Mail::send('mail.login-credentials', ['email' => $request->input('email'), 'password' => $request->input('password')], function($message) use($request){
            $message->to($request->email);
            $message->subject('Login details');
        });
        return response()->json(['message' => 'Staff created successfully', 'data' => $staff], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create staff', 'message' => $e->getMessage()], 500);
    }
}

public function studentDelete($id)
{
    $student = Student::findOrFail($id);
    $student->delete();
    return response()->json(null, 204);
}

public function staffDelete($id)
{
    $staff = Staff::findOrFail($id);
    $staff->delete();
    return response()->json(null, 204);
}

public function finalStudentUpdate(Request $request, $id)
{
   
    $student = Student::find($id);
    if (!$student) {
        return response()->json([
            'message' => "Student not found",
        ], 404);
    }

    $student->id_number = $request->input('id_number');
    $student->student = $request->input('student');
    $student->fname = $request->input('fname');
    $student->lname = $request->input('lname');
    $student->email = $request->input('email');
    
    if ($request->hasFile('file')) {
        // Store the file and set the file attribute
        $student->file = $request->file('file')->store('pictures');
    }
    $student->save();
    return response()->json([
        'message' => "Student successfully updated",
    ]);
}

public function finalStaffUpdate(Request $request, $id)
{
   
    $staff = Staff::find($id);
    if (!$staff) {
        return response()->json([
            'message' => "Student not found",
        ], 404);
    }

    $signatureDataUrl = $request->input('imageURL');
        $signatureData = explode(',', $signatureDataUrl)[1];
        $imageData = base64_decode($signatureData);
        $signatureFilename = 'signature_' . uniqid() . '.png';
        File::put(storage_path('app/public/' . $signatureFilename), $imageData);

    $staff->id_number = $request->input('id_number');
    $staff->staff = $request->input('staff');
    $staff->fname = $request->input('fname');
    $staff->lname = $request->input('lname');
    $staff->email = $request->input('email');
    $staff->$signatureFilename;
    
    if ($request->hasFile('file')) {
        // Store the file and set the file attribute
        $staff->file = $request->file('file')->store('pictures');
    }
    $staff->save();
    return response()->json([
        'message' => "Student successfully updated",
    ]);
}


public function studentUpdate(Request $request, $id)
   {
       $student = Student::findOrFail($id);
       return response()->json([
        'student'=> $student,
    ]);
   }

   public function staffUpdate(Request $request, $id)
   {
      $staff = Staff::findOrFail($id);
       return response()->json([
        'staff'=>$staff,
    ]);
   }



  public function showStaff(){
    return Staff::all();
  }

  public function showStudent(){
    return Student::all();
  } 
   

  public function addStudent(Request $request)
{
    $rules = [
        'id_number' => 'required|string|max:255',
        'student' => 'required|string|max:255',
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'email' => 'required|email|unique:admins|max:255',
        'password' => 'required|string|min:8',
        'file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ];

    // Custom error messages
    $messages = [
        'id_number.required' => 'id_number is required.',
        'student.required' => 'id_number is required.',
        'fname.required' => 'first name is required.',
        'lname.required' => 'last name is required.',
        'email.required' => 'Email is required.',
        'email.email' => 'Invalid email format.',
        'email.unique' => 'Email already exists.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters long.',
        'file' => 'file is required.',
    ];

    
    $validator = Validator::make($request->all(), $rules, $messages);

    
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    try {
        $student = Student::create([
            'id_number' => $request->input('id_number'),
            'student' => $request->input('student'),
            'fname' => $request->input('fname'),
            'lname' => $request->input('lname'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'file' => $request->file('file')->store('pictures'),
        ]);
        
        $token = $student->createToken('student-token', ['actAsStudent'])->plainTextToken;
        Mail::send('mail.Student-login', ['email' => $request->input('email'), 'password' => $request->input('password')], function($message) use($request){
            $message->to($request->email);
            $message->subject('Login details');
        });

        return response()->json(['message' => 'Student created successfully', 'data' => $student], 201);


    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create student', 'message' => $e->getMessage()], 500);
    }
   
}

public function adminUpdate(Request $request)
{
    $admin = Admin::where('email', $request->input('email'))->first();

    if ($admin) {
        $admin->fname = $request->input('fname');
        $admin->lname = $request->input('lname');
        $admin->email = $request->input('email');
        $admin->password = bcrypt($request->input('password')); 

        $admin->save();

        return response()->json([
            'message' => "Admin successfully updated",
        ]);
    } else {
        return response()->json([
            'message' => "Admin not found",
        ], 404);
    }
}


}
