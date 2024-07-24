<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Requirement;
use App\Models\StaffRequirement;
use App\Models\StudentRequirement;


class StaffAuthController extends Controller

{

    public function chartStaff()
    {
        $data = Staff::selectRaw("date_format(created_at, '%Y-%m-%d') as created_at_date, count(*) as account_count")
            ->whereDate('created_at', '>=', now()->subDays(1))
            ->groupBy('created_at_date')
            ->get();
    
        return response()->json($data);
    }
    
    public function studentDone(Request $request)
{
    $requirement = Requirement::where('id', $request->input('id'))->first();

    if ($requirement) {
        $requirement->status = $request->input('status');
        $requirement->requirements = $request->input('requirements');
       
        $requirement->save();

        return response()->json([
            'message' => "Admin successfully updated",
        ]);
    } else {
        return response()->json([
            'message' => "Admin not found",
        ], 404);
    }
}



public function login(Request $request)
{
    $staff = Staff::where('email', $request->input('email'))->first();

    if (!$staff || !Hash::check($request->input('password'), $staff->password)) {
        return response()->json(['message' => 'Email or password incorrect'], 401);
        
    }
    $token = $staff->createToken('staff-token', ['actAsStaff'])->plainTextToken;
    return response()->json(['token' => $token, 'staff' => $staff], 200);
}


public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully'], 200);
}
   
public function saveEvaluation(Request $request) {

    $validatedData = $request->validate([
        'status' => 'required|in:done,not',
        'requirements' => 'required|array',
    ]);

    $requirement = Requirement::create([
        'status' => $validatedData['status'],
        'requirements' => $validatedData['requirements'],
    ]);

    return response()->json(['data' =>  $requirement], 201);
}

public function staffRequirement(Request $request)
    {
        
        $validatedData = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'requirements_id' => 'required|exists:requirements,id',
        ]);

        
        $staffRequirement = StaffRequirement::create([
            'staff_id' => $validatedData['staff_id'],
            'requirements_id' => $validatedData['requirements_id'],
        ]);

        
        return response()->json(['message' => 'Staff requirement created successfully'], 201);
    }

    public function studentRequirement(Request $request)
    {
        
        $validatedData = $request->validate([
            'students_id' => 'required|exists:students,id',
            'requirements_id' => 'required|exists:requirements,id',
        ]);

        
        $staffRequirement = StudentRequirement::create([
            'students_id' => $validatedData['students_id'],
            'requirements_id' => $validatedData['requirements_id'],
        ]);

        
        return response()->json(['message' => 'Student requirement created successfully'], 201);
    }
    
    public function showStaff(){
       
        $staffRequirements = StaffRequirement::with('staff', 'requirement')->get();
        return response()->json($staffRequirements);

      }

      public function showStudent(){
       
        $studentRequirements = StudentRequirement::with('student', 'requirement')->get();
        return response()->json($studentRequirements);
        
      }

      public function studentEvaluation(Request $request, $id)
      {
          $evaluation = Student::find($id);
          $evaluation->evaluate = 'done';
          $evaluation->save();
  
          return response()->json(['message' => 'Evaluation updated successfully']);
      }



      public function studentUpdateEvaluation(Request $request)
    {
    
        $requirement = Requirement::findOrFail($request->input('id'));

        $requirement->status = $request->input('status');
        $requirement->requirements = $request->input('requirements');
       
        $requirement->save();

        return response()->json([
            'message' => "Updated successfully updated",
        ]);
}

}
