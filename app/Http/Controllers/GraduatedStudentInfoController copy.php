<?php

namespace App\Http\Controllers;

use App\Models\GraduatedStudentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraduatedStudentInfoController extends Controller
{
    // Show form
    public function index()
    {
        $info = GraduatedStudentInfo::where('user_id', Auth::id())->first();

        return view('graduated-student.form', compact('info'));
    }

    // Store / Update
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'father_name' => 'required',
            'mother_name' => 'required',
            'class_roll' => 'required',
            'hsc_roll' => 'required',
            'session' => 'required',
            'institution_name' => 'required',
            'mobile' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:100', // 100 KB
        ]);

        // Check if user already exists
        $user = GraduatedStudentInfo::where('user_id', Auth::id())->first();

        $data = $request->except('photo');

        // Handle user_id
        if ($user) {
            // Existing user, keep the same user_id
            $newUserId = $user->user_id;
        } else {
            // New user, generate sequential user_id
            $lastUser = GraduatedStudentInfo::orderBy('user_id', 'desc')->first();
            $lastNumber = $lastUser ? $lastUser->user_id : 26000;
            $newUserId = $lastNumber + 1;
        }

        $data['user_id'] = $newUserId;

        // Handle photo upload
        if ($request->hasFile('photo')) {

            $directory = public_path('graduated-student');

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $extension = $request->file('photo')->getClientOriginalExtension();

            // Use user_id as filename
            $fileName = $newUserId . '.' . $extension;

            // Move uploaded file
            $request->file('photo')->move($directory, $fileName);

            $data['photo'] = 'graduated-student/' . $fileName;
        } else {
            // Keep existing photo if updating
            if ($user && !empty($user->photo)) {
                $data['photo'] = $user->photo;
            }
        }

        if ($user) {
            // Update existing user
            $user->update($data);
        } else {
            // Create new user
            GraduatedStudentInfo::create($data);
        }

        return redirect()->back()->with('success', 'Information saved successfully!');
    }
}
