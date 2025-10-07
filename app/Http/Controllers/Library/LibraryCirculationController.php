<?php

namespace App\Http\Controllers\Library;

use App\Libs\Study;
use App\Models\Libmember;
use App\Models\Maccession;
use App\Models\Circulation;
use App\Models\LibraryUser;
use Illuminate\Http\Request;
use App\Models\Libcirculation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LibraryCirculationController extends Controller
{
    public function index() {
		$title = 'Easy CollegeMate - Library Circulation';
		$breadcrumb = 'library.circulation.index:Library Circulation|Dashboard';
		$libcirculations = Libcirculation::paginate(Study::paginate());
		$book_status_lists = ['' => 'Select Book Status', 1 => 'Issued', 2 => 'Returned'];

		return view('BackEnd.library.circulation.index',compact('title','breadcrumb','libcirculations','book_status_lists'));

	}



	public function check() {

		$title = 'Easy ColllegeMate - Library Circulation';
		$breadcrumb = 'library.circulation.index:Library Circulation|Issue-Return Book';
		$libraryuser_lists = ['' => 'Select User Type'] + LibraryUser::pluck('user_type', 'id')->toArray();

		return view('BackEnd.library.circulation.check',compact('title','breadcrumb','libraryuser_lists'));

	}



	public function checkpost(Request $request) {
		$libraryuser_id = Study::filterInput('libraryuser_id', $request->get('libraryuser_id'));
		$libmember_id = Study::filterInput('libmember_id', $request->get('libmember_id'));		

		if((!isset($libraryuser_id)) || (!isset($libmember_id))) :
			return Redirect::route('library.circulation.check');
		endif;	

		if(isset($_POST['libraryuser_id']) && isset($_POST['libmember_id'])) :
			$data = $request->all();
			$validation = Libcirculation::checkValidate($data);

			if($validation->fails()) :
				return Redirect::back()->withInput()->withErrors($validation);
			endif;	

			//Check Library User Exist
			$libraryuser_exist = LibraryUser::whereId($libraryuser_id)->count();

			if($libraryuser_exist == 0) :
				$error_message = 'Invalid library user type';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;			

			//Check User exist or Not
			$member_exist = Libmember::whereId($libmember_id)->count();

			if($member_exist == 0) :
				$error_message = 'Invalid ID No, Not Found';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;

			//Match with member and user type
			$member_usertype = Libmember::whereId($libmember_id)->whereLibraryuser_id($libraryuser_id)->count();

			if($member_usertype == 0) :
				$error_message = 'The member is not associated with the user type! Please try again';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;	
		endif;	

		$title = 'Easy CollegeMate - Library Circulation';
		$breadcrumb = 'library.circulation.index:Library Circulation|Issue-Return Book';
		$libraryuser_lists = ['' => 'Select User Type'] + LibraryUser::pluck('user_type', 'id')->toArray();		
		$circulation = Circulation::whereLibraryuser_id($libraryuser_id)->first();
		$libcirculations = Libcirculation::whereLibmember_id($libmember_id)->whereStatus(1)->get();

		return view('BackEnd.library.circulation.create',compact('title', 'breadcrumb','libraryuser_lists','libmember_id','libraryuser_id','circulation','libcirculations'));

	}



	public function store(Request $request) {

		$data = $request->all();
		$validation = Libcirculation::customValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		$maccession_id = Maccession::whereAccession_no($request->get('accession_no'))->pluck('id')->first();

		if($maccession_id == NULL) :
			$error_message = 'Invalid accession no';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;	

		$libmember_id = $request->get('libmember_id');

		$libraryuser_id = Libmember::whereId($libmember_id)->pluck('libraryuser_id')->first();
		$maximum_issue = Circulation::whereLibraryuser_id($libraryuser_id)->pluck('maximum_issue')->first();
		$no_of_books_issued = Libcirculation::whereLibmember_id($libmember_id)->whereStatus(1)->count();

		//Maximum book issue checker
		if($no_of_books_issued >= $maximum_issue) :
			$error_message = 'The member already issued maximum ' . $maximum_issue . ' no of books';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;	

		//The book in issued status :: Checker
		$is_this_book_issued = Libcirculation::whereMaccession_id($maccession_id)->whereStatus(1)->count();

		if($is_this_book_issued != 0) :
			$error_message = 'The book is in issued status';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;	

		//Calculate Issued date and return date
		$issued_days = Circulation::whereLibraryuser_id($libraryuser_id)->pluck('issued_days')->first();

		$issue_date = date('Y-m-d', time());
		$return_date = date('Y-m-d', strtotime($issue_date . '+' . $issued_days . ' days'));

		//Insert Library Circulation
		$libcirculation = new Libcirculation;
		$libcirculation->libmember_id = $request->get('libmember_id');
		$libcirculation->maccession_id = $maccession_id;
		$libcirculation->issue_date = $issue_date;
		$libcirculation->return_date = $return_date;
		$libcirculation->status = 1;	
		$libcirculation->save();

		$message = 'You have successfully issued the book with the Id';		

		return Redirect::back()->with('success',$message);

	}



	public function returnBook() {

		$libmember_id_checker = Session::has('libmember_id') ? Session::get('libmember_id') : NULL;

		$libmember_id = $request->get('libmember_id');

		if($libmember_id != $libmember_id_checker) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$issued_books = Libcirculation::whereLibmember_id($libmember_id)->whereStatus(1)->get();

		if($issued_books->count() == 0) :
			$error_message = 'There is no book issued with this Id ' . $libmember_id . ' now';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$how_many_return_books = 0;
		$member_return_date = date('Y-m-d', time());

		foreach($issued_books as $issued_book) :
			$libcirculation_id = $issued_book->id;
			if($request->get($libcirculation_id) == $libcirculation_id) :
				$how_many_return_books++;
				$libcirculation = Libcirculation::find($libcirculation_id);
				$libcirculation->status = 2;
				$libcirculation->member_return_date = $member_return_date;
				$libcirculation->update();
			endif;
		endforeach;	

		if($how_many_return_books == 0) :
			$error_message = 'You have not selected any book to return';
			return Redirect::back()->with('error',$error_message);
		endif;	

		if($how_many_return_books == 1) :
			$message = 'You have returned the book';
			return Redirect::back()->with('success',$message);
		endif;
		
		if($how_many_return_books > 1) :
			$message = 'You have returned ' . $how_many_return_books . ' no of books';
			return Redirect::back()->with('success',$message);
		endif;		
		
	}



	public function show($id) {

		$libcirculation = Libcirculation::find($id);

		$title = 'Easy CollegeMate - Library Circulation - Member Id - ' . $libcirculation->libmember_id;
		$breadcrumb = 'library.circulation.index:Library Circulation|Member Id - ' . $libcirculation->libmember_id;

		return view('BackEnd.library.circulation.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withLibcirculation($libcirculation);		

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Library Circulation';
		$breadcrumb = 'library.circulation.index:Library Circulation|Edit Circulation';
		$libcirculation = Libcirculation::find($id);

		//Issued Days
		$libmember_id = $libcirculation->libmember_id;
		$libraryuser_id = Libmember::whereId($libmember_id)->pluck('libraryuser_id')->first();		
		$issued_days = Circulation::whereLibraryuser_id($libraryuser_id)->pluck('issued_days')->first();	
		$status_lists = [1 => 'Issued', 2 => 'Returned'];	

		return view('BackEnd.library.circulation.edit', compact('title','breadcrumb','libcirculation','issued_days','status_lists'));

	}



	public function update($id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$data = $request->all();
		$validation = Libcirculation::updateValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		$accession_no_exist = Maccession::whereAccession_no($request->get('accession_no'))->count();

		if($accession_no_exist == 0) :
			$error_message = 'Invalid accession no';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;	

		$maccession_id = Maccession::whereAccession_no($request->get('accession_no'))->pluck('id')->first();

		$libcirculation = Libcirculation::find($id);
		$libcirculation->maccession_id = $maccession_id;
		$libcirculation->issue_date = $request->get('issue_date');
		$libcirculation->return_date = $request->get('return_date');
		$libcirculation->status = $request->get('status');
		$libcirculation->update();

		//Page
		$count = Libcirculation::where('id', '<=', $id)->count();
		$page = ceil($count/Study::paginate());

		$message = 'You have successfully updated the library circulation';
		return Redirect::route('library.circulation.index', ['page' => $page])
						->with('info',$message)
						->withId($id);

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$id = $request->get('id');
		$libcirculation = Libcirculation::find($id); 
		$libcirculation->delete();

		$error_message = 'You have deleted the library circulation';
		return Redirect::back()->with('warning',$error_message);

	}



	public function search(Request $request) {

		$title = 'Easy CollegeMate - Library Circulation';
		$breadcrumb = 'library.circulation.index:Library Circulation|Dashboard';

		//Search Library Circulation
		$status = Study::filterInput('status', $request->get('status'));
		$libmember_id = Study::filterInput('libmember_id', $request->get('libmember_id'));
		$accession_no = Study::filterInput('accession_no', $request->get('accession_no'));
		$call_no = Study::filterInput('call_no', $request->get('call_no'));

		$libcirculations = Study::searchLibcirculation($status, $libmember_id, $accession_no, $call_no);

		//Form Element
		$book_status_lists = ['' => 'Select Book Status', 1 => 'Issued', 2 => 'Returned'];

		return view('BackEnd.library.circulation.search',compact('title','breadcrumb','libcirculations','book_status_lists','status','libmember_id','accession_no','call_no'));		

	}
}
