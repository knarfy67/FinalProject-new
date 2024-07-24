<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;


class StudentAuthController extends Controller
{
  
    public function chartStudent() {
        $data = Student::selectRaw('DATE(created_at) as created_at_date, COUNT(*) as account_count')
        ->groupBy('created_at_date')
        ->orderBy('created_at_date')
        ->get();
       
        return response()->json(['data' => $data ]);
    }


public function login(Request $request)
{
    $student = Student::where('email', $request->input('email'))->first();

    if (!$student || !Hash::check($request->input('password'), $student->password)) {
        return response()->json(['message' => 'Email or password incorrect', ], 401);
    }
    $token = $student->createToken('student-token', ['actAsStudent'])->plainTextToken;
    return response()->json(['token' => $token, 'student' => $student], 200);

}

public function studentGet($id){
    $student = Student::findOrFail($id);
    return response()->json(['data' => $student], 200);
}



public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully'], 200);
}


}
