<?php

namespace App\Http\Controllers\StudentPanel;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\StudentDocument;
use App\Models\DocumentPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class StudentController extends Controller
{
    /**
     * Display the student dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $student = Auth::guard('student')->user();
        
        // Get recent document payments
        $recentPayments = DocumentPayment::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->with('documentType')
            ->get();
            
        // Get available document types for this student's course and session
        $availableDocuments = DocumentType::where('is_active', true)
            ->where('course', $student->course)
            ->where('session', $student->session)
            ->get();
        
        // Get count of purchased documents
        $purchasedDocumentsCount = DocumentPayment::where('student_id', $student->id)
            ->where('status', 'paid')
            ->count();
        
        // Get count of downloaded documents
        $downloadedDocumentsCount = DocumentPayment::where('student_id', $student->id)
            ->where('status', 'paid')
            ->where('download_count', '>', 0)
            ->count();
        
        // Get count of pending payments
        $pendingPaymentsCount = DocumentPayment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->count();
        
        // Get paid document IDs for easy checking
        $paidDocumentIds = DocumentPayment::where('student_id', $student->id)
            ->where('status', 'paid')
            ->pluck('document_type_id')
            ->toArray();
        
        // Get pending document IDs
        $pendingDocumentIds = DocumentPayment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->pluck('document_type_id')
            ->toArray();
        
        // Get notifications count (placeholder - implement your notification system)
        $notificationsCount = 0; // Replace with actual notification count
        
        return view('student.dashboard', compact(
            'student', 
            'recentPayments', 
            'availableDocuments', 
            'purchasedDocumentsCount',
            'downloadedDocumentsCount',
            'pendingPaymentsCount',
            'paidDocumentIds',
            'pendingDocumentIds',
            'notificationsCount'
        ));
    }
    
    /**
     * Display the student profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $student = Auth::user();
        return view('students.profile', compact('student'));
    }
    
    /**
     * Show the form for editing the student profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function editProfile()
    {
        $student = Auth::user();
        return view('students.edit-profile', compact('student'));
    }
    
    /**
     * Update the student profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $student = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'name_in_bengali' => 'required|string|max:255',
            'fathers_name' => 'required|string|max:255',
            'fathers_name_in_bengali' => 'required|string|max:255',
            'mothers_name' => 'required|string|max:255',
            'mothers_name_in_bengali' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $student->id,
            'mobile' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'village_in_bengali' => 'required|string|max:255',
            'post_office_in_bengali' => 'required|string|max:255',
            'thana_in_bengali' => 'required|string|max:255',
            'district_in_bengali' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($student->profile_photo) {
                Storage::delete('public/' . $student->profile_photo);
            }
            
            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $student->profile_photo = $path;
        }
        
        // Update student information
        $student->name = $request->name;
        $student->name_in_bengali = $request->name_in_bengali;
        $student->fathers_name = $request->fathers_name;
        $student->fathers_name_in_bengali = $request->fathers_name_in_bengali;
        $student->mothers_name = $request->mothers_name;
        $student->mothers_name_in_bengali = $request->mothers_name_in_bengali;
        $student->email = $request->email;
        $student->mobile = $request->mobile;
        $student->date_of_birth = $request->date_of_birth;
        $student->gender = $request->gender;
        $student->village_in_bengali = $request->village_in_bengali;
        $student->post_office_in_bengali = $request->post_office_in_bengali;
        $student->thana_in_bengali = $request->thana_in_bengali;
        $student->district_in_bengali = $request->district_in_bengali;
        
        $student->save();
        
        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Show the form for changing password.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword()
    {
        return view('students.change-password');
    }
    
    /**
     * Update the student's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);
        
        $student = Auth::user();
        
        // Check if current password is correct
        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        // Update password
        $student->password = Hash::make($request->password);
        $student->save();
        
        return redirect()->route('student.profile')->with('success', 'Password changed successfully!');
    }
    
    /**
     * Display payment history.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentHistory()
    {
        $student = Auth::user();
        $payments = DocumentPayment::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('students.payment-history', compact('payments'));
    }
}