<?php

namespace App\Http\Controllers\Library;

use App\Libs\Study;
use App\Models\Libmember;
use App\Models\LibraryUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class LibraryMemberController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Library Member';
		$breadcrumb = 'library.member.index:Library Member|Dashboard';
		$libraryuser_lists = ['' => 'Select User Type'] + LibraryUser::pluck('user_type', 'id')->toArray();		
		$libmembers = Libmember::paginate(Study::paginate());
        $member_id = '';
        $full_name = '';
        $libraryuser_id = '';

		return view('BackEnd.library.member.index', compact('title', 'breadcrumb', 'libraryuser_lists','libmembers', 'member_id','full_name', 'libraryuser_id'));

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Library Member';
		$breadcrumb = 'library.member.index:Library Member|Add New Member';
		$libraryuser_lists = ['' => 'Select User Type'] + LibraryUser::pluck('user_type', 'id')->toArray();

		return view('BackEnd.library.member.create', compact('title', 'breadcrumb','libraryuser_lists'));

	}



	public function store(Request $request) {

		$data = $request->all();
		$validation = Libmember::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		//Insert Library Member
		$libmember = new Libmember;
		$libmember->libraryuser_id = $request->get('libraryuser_id');
		$libmember->full_name = $request->get('full_name');
		$libmember->date_of_birth = $request->get('date_of_birth');
		$libmember->contact_no = $request->get('contact_no');
		$libmember->gender = $request->get('gender');
		$libmember->save();

		//Page
		$page = ceil(Libmember::count()/Study::paginate());

		$id = $libmember->id;

		$message = 'You have successfully created new library member';
		return Redirect::route('library.member.index', ['page' => $page])
						->with('success',$message)
						->withId($id);

	}



	public function show($id) {

		$libmember = Libmember::find($id);
		$title = "Easy CollegeMate - Library Member - " . $libmember->full_name;
		$breadcrumb = 'library.member.index:Library Member|Member - ' . $libmember->full_name;

		return view('library.member.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withLibmember($libmember);

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Library Member';
		$breadcrumb = 'library.member.index:Library Member|Edit Member';
		$libraryuser_lists = ['' => 'Select User Type'] + LibraryUser::pluck('user_type', 'id')->toArray();		
		$libmember = Libmember::find($id);

		return view('BackEnd.library.member.edit', compact('title', 'breadcrumb', 'libraryuser_lists','libmember'));

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$data = $request->all();
		$validation = Libmember::updateValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		//Update Library Member
		$libmember = Libmember::find($id);
		$libmember->libraryuser_id = $request->get('libraryuser_id');
		$libmember->full_name = $request->get('full_name');
		$libmember->date_of_birth = $request->get('date_of_birth');
		$libmember->contact_no = $request->get('contact_no');
		$libmember->gender = $request->get('gender');
		$libmember->update();

		//Page
		$count = Libmember::where('id', '<=', $id)->count();
		$page = ceil($count/Study::paginate());

		$message = 'You have successfully updated the library member';
		return Redirect::route('library.member.index', ['page' => $page])
						->with('info',$message)
						->withId($id);

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$libmember = Libmember::find($id);
		$libmember->delete();

		$error_message = 'You have deleted the library member';
		return Redirect::back()->with('warning',$error_message);		

	}



	public function search(Request $request) {

		$title = 'Easy CollegeMate - Library Member';
		$breadcrumb = 'library.member.index:Library Member|Dashboard';

		//Search Material
		$member_id =  $request->get('member_id');
		$full_name = $request->get('full_name');
		$libraryuser_id = $request->get('libraryuser_id');

		$libmembers = Study::searchMember($member_id, $full_name, $libraryuser_id);

		//Form Element
		$libraryuser_lists = ['' => 'Select User Type'] + LibraryUser::pluck('user_type', 'id')->toArray();		

		return view('BackEnd.library.member.index', compact('title','breadcrumb','libraryuser_lists', 'libmembers','member_id', 'full_name', 'libraryuser_id'));		

	}
}
