<?php
    
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class StudentAuthController extends Controller
{
    /**
     * Show the login form
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('student.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $remember = $request->has('remember');
        
        // Initialize credentials array with password
        $credentials = ['password' => $password];
        
        // Determine which field to use for authentication
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            // Email format
            $field = 'email';
        } 
        // Check if username starts with 0, treat as mobile number
        elseif (substr($username, 0, 1) === '0') {
            $field = 'mobile';
        }
        // Check if username is a mobile number (assuming it's numeric and has appropriate length)
        elseif (preg_match('/^([0-9\s\-\+\(\)]*)$/', $username) && 
                strlen(preg_replace('/[^0-9]/', '', $username)) >= 10) {
            // Mobile number format
            $field = 'mobile';
        } else {
            // Student ID format
            $field = 'student_id';
        }
        
        // Add the appropriate field to credentials
        $credentials[$field] = $username;
        
        // Attempt authentication
        if (Auth::guard('student')->attempt($credentials, $remember)) {
            $user = Auth::guard('student')->user();
            
            // Handle redirect
            $intended = redirect()->intended()->getTargetUrl();
            $defaultRoute = 'student.dashboard';
            
            if (in_array($intended, [url('student/dashboard'), url('/')])) {
                return redirect()->route($defaultRoute);
            }
            
            return redirect()->intended(route($defaultRoute));
        }
        
        // For debugging purposes, let's try to find the student directly
        $student = Student::where($field, $username)->first();
        if ($student) {
            \Log::info('Student found but authentication failed', [
                'id' => $student->id,
                'password_matches' => Hash::check($password, $student->password)
            ]);
        } else {
            \Log::info('No student found with ' . $field . ' = ' . $username);
        }
        
        return redirect()->route('student.login')
            ->withInput($request->only('username'))
            ->withErrors(['username' => 'These credentials do not match our records.']);
    }

    /**
     * Show the registration form
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('student.auth.register');
    }

    /**
     * Handle student registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'required|string|max:255',
            'mobile' => 'required|string|unique:students,mobile',
            'password' => 'required|string|min:6|confirmed',
            'fathers_name' => 'required|string|max:255',
            'fathers_name_bn' => 'required|string|max:255',
            'mothers_name' => 'required|string|max:255',
            'mothers_name_bn' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'post_office_bn' => 'required|string|max:255',
            'upazila_bn' => 'required|string|max:255',
            'district_bn' => 'required|string|max:255',
            'course' => ['required', Rule::in(['hsc', 'honours', 'masters', 'degree'])],
            'session' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'class_roll' => 'required|string|max:255',
            'registration_no' => 'required|string|max:255|unique:students,registration_no',
        ]);

        // Create the student
        $student = new Student();
        $student->name = $validated['name'];
        $student->name_bn = $validated['name_bn'];
        $student->mobile = $validated['mobile'];
        $student->password = Hash::make($validated['password']);
        $student->normal_password = $validated['password']; // Storing plain password (not recommended for production)
        $student->fathers_name = $validated['fathers_name'];
        $student->fathers_name_bn = $validated['fathers_name_bn'];
        $student->mothers_name = $validated['mothers_name'];
        $student->mothers_name_bn = $validated['mothers_name_bn'];
        $student->birth_date = date('Y-m-d',strtotime($validated['birth_date']));
        $student->post_office_bn = $validated['post_office_bn'];
        $student->upazila_bn = $validated['upazila_bn'];
        $student->district_bn = $validated['district_bn'];
        $student->course = $validated['course'];
        $student->session = $validated['session'];
        $student->level = $validated['level'];
        $student->class_roll = $validated['class_roll'];
        $student->registration_no = $validated['registration_no'];
        $student->is_approved = 0; // Default to not approved
        $student->save();

        // Auto-login the student after registration
        Auth::guard('student')->login($student);
        
        // Check if auto-approval is needed
        if (!$student->is_approved) {
            // If approval is required, show a message but keep them logged in
            return redirect()->route('student.dashboard')
                ->with('warning', 'Your account has been created and you are now logged in. However, some features may be limited until an administrator approves your account.');
        }

        // If no approval needed or auto-approved
        return redirect()->route('student.dashboard')
            ->with('success', 'Registration successful! You are now logged in.');
    }

    /**
     * Show the student dashboard
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        if (!Session::has('student_id')) {
            return redirect()->route('student.login');
        }

        $student = Student::findOrFail(Session::get('student_id'));

        if (!$student->is_approved) {
            Session::flush();
            return redirect()->route('student.login')
                ->with('error', 'Your account is not approved yet. Please contact the administrator.');
        }

        return view('students.dashboard', compact('student'));
    }

    /**
     * Log the student out
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Session::flush();
        return redirect()->route('student.login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the student profile
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        if (!Session::has('student_id')) {
            return redirect()->route('student.login');
        }

        $student = Student::findOrFail(Session::get('student_id'));
        return view('students.profile', compact('student'));
    }

    /**
     * Show the edit profile form
     *
     * @return \Illuminate\View\View
     */
    public function editProfile()
    {
        if (!Session::has('student_id')) {
            return redirect()->route('student.login');
        }

        $student = Student::findOrFail(Session::get('student_id'));
        return view('students.edit-profile', compact('student'));
    }

    /**
     * Update the student profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        if (!Session::has('student_id')) {
            return redirect()->route('student.login');
        }

        $student = Student::findOrFail(Session::get('student_id'));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'required|string|max:255',
            'mobile' => ['required', 'string', Rule::unique('students')->ignore($student->id)],
            'fathers_name' => 'required|string|max:255',
            'fathers_name_bn' => 'required|string|max:255',
            'mothers_name' => 'required|string|max:255',
            'mothers_name_bn' => 'required|string|max:255',
            'post_office_bn' => 'required|string|max:255',
            'thana_bn' => 'required|string|max:255',
            'district_bn' => 'required|string|max:255',
        ]);

        $student->update($validated);

        return redirect()->route('student.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the change password form
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        if (!Session::has('student_id')) {
            return redirect()->route('student.login');
        }

        return view('students.change-password');
    }

    /**
     * Update the student's password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        if (!Session::has('student_id')) {
            return redirect()->route('student.login');
        }

        $student = Student::findOrFail(Session::get('student_id'));

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $student->password = Hash::make($request->password);
        $student->normal_password = $request->password; // Storing plain password (not recommended for production)
        $student->save();

        return redirect()->route('student.profile')
            ->with('success', 'Password changed successfully.');
    }
}
