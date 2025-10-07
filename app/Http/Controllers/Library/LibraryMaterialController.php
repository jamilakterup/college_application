<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\BookInfo;
use App\Models\Department;
use App\Models\Maccession;
use App\Models\Material;
use App\Models\Msubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class LibraryMaterialController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Library Material';
		$breadcrumb = 'library.material.index:Library Material|Dashboard';
		$maccessions = Maccession::paginate(Study::paginate());

        $physical_form = '';
		$accession_no = '';
		$call_no = '';
		$book_title = '';
		$author ='';

		//Form Element
		$physical_form_lists = ['' => 'Select physical form','book' => 'Book','journal' => 'Journal','cd/dvd' => 'CD/DVD','manuscript' => 'Manuscript','others' => 'Others'];		

		return view('BackEnd.library.material.index', compact('title', 'breadcrumb','maccessions','physical_form_lists','physical_form','accession_no','call_no','book_title','author'));

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Material';
		$breadcrumb = 'library.material.index:Library Material|Add New Material';

		//Form Element
		$physical_form_lists = ['' => 'Select physical form','book' => 'Book','journal' => 'Journal','cd/dvd' => 'CD/DVD','manuscript' => 'Manuscript','others' => 'Others'];
		$size_lists = ['' => 'Select book size','tiny' => 'Tiny','small' => 'Small','medium' => 'Medium','large' => 'Large','huge' => 'Huge'];
		$source_details_lists = ['' => 'Select source details','local' => 'Local','university' => 'University','dge_donation' => 'DGE Donation','personal_donation' => 'Personal Donation','others' => 'Others'];
		$subjects = Department::all();

		return view('BackEnd.library.material.create',compact('title','breadcrumb','physical_form_lists','size_lists','source_details_lists','subjects'));

	}



	public function store(Request $request) {

		$data = $request->all();
		$validation = Material::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		//Accession Number checker
		$initial_accession_no = $request->get('accession_no');
		$no_of_books = $request->get('no_of_books');
		$final_accession_no = $initial_accession_no + $no_of_books - 1;

		foreach(range($initial_accession_no, $final_accession_no) as $index) :
			$accession_no_exists = Maccession::whereAccession_no($index)->count();
			if($accession_no_exists > 0) :
				$error_message = 'The accession no ' . $index . ' is already taken';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif; 
		endforeach;		

		//Insert Material
		$material = new Material;
		$material->isbn = $request->get('isbn');
		$material->call_no = $request->get('call_no');
		$material->physical_form = $request->get('physical_form');
		$material->title = $request->get('title');
		$material->subtitle = $request->get('subtitle');
		$material->author = $request->get('author');
		$material->editor = $request->get('editor');
		$material->edition = $request->get('edition');
		$material->edition_year = $request->get('edition_year');
		$material->publisher = $request->get('publisher');
		$material->publishing_year = $request->get('publishing_year');
		$material->place_of_publication = $request->get('place_of_publication');
		$material->location = $request->get('location');
		$material->price = $request->get('price');
		$material->series = $request->get('series');
		$material->size = $request->get('size');
		$material->no_of_pages = $request->get('no_of_pages');
		$material->clue_page = $request->get('clue_page');
		$material->no_of_books = $request->get('no_of_books');
		$material->source_details = $request->get('source_details');
		$material->note = $request->get('note');
		$material->save();

		$material_id = $material->id;

		//Insert Maccession
		foreach(range($initial_accession_no,$final_accession_no) as $index) :

			$maccession = new Maccession;
			$maccession->material_id = $material_id;
			$maccession->accession_no = $index;
			$maccession->save();

		endforeach;	

		//Insert Msubject
		$subjects_id = [];
		$subjects = Department::get();

		if($subjects->count() > 0) :
			foreach($subjects as $subject) :
					$s_id = $subject->id;
					if($request->get($s_id) == $s_id) :
						$subjects_id[] = $s_id;
					endif;
			endforeach;		

			if(count($subjects_id) > 0) :
				foreach($subjects_id as $subject_id) :
						$msubject = new Msubject;
						$msubject->subject_id = $subject_id;
						$msubject->material_id = $material_id;
						$msubject->save();
				endforeach;
			endif;	
		endif;	

		//Page
		$page = ceil(Maccession::count()/Study::paginate());

		$id = $material_id;

		$message = 'You have successfully created new library material';

		return Redirect::route('library.material.index',['page' => $page])
						->with('success',$message)
						->withId($id);

	}



	public function show($id) {

		$maccession = Maccession::find($id);
		$title = 'Easy CollegeMate - Library Material - ' . $maccession->material->title;
		$breadcrumb = 'library.material.index:Library Material|Material - ' . $maccession->material->title;

		//Page
		$count = Maccession::where('id', '<=', $id)->count();
		$page = ceil($count/Study::paginate());		

		return view('library.material.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withMaccession($maccession)
					->withPage($page);

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Library Material';
		$breadcrumb = 'library.material.index:Library Material|Edit Material';
		$maccession = Maccession::find($id);
		$material = Material::find($maccession->material_id);

		//Form Element
		$physical_form_lists = ['' => 'Select physical form','book' => 'Book','journal' => 'Journal','cd/dvd' => 'CD/DVD','manuscript' => 'Manuscript','others' => 'Others'];
		$size_lists = ['' => 'Select book size','tiny' => 'Tiny','small' => 'Small','medium' => 'Medium','large' => 'Large','huge' => 'Huge'];
		$source_details_lists = ['' => 'Select source details','local' => 'Local','university' => 'University','dge_donation' => 'DGE Donation','personal_donation' => 'Personal Donation','others' => 'Others'];
		$subjects = Department::all();		

		return view('BackEnd.library.material.edit',compact('title','breadcrumb','material','maccession','physical_form_lists','size_lists','source_details_lists','subjects'));

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		$data = $request->all();
		$validation = Material::updateValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;	

		$maccession = Maccession::find($id);
		$material_id = $maccession->material_id;

		//Material Update
		$material = Material::find($material_id);
		$material->isbn = $request->get('isbn');
		$material->call_no = $request->get('call_no');
		$material->physical_form = $request->get('physical_form');
		$material->title = $request->get('title');
		$material->subtitle = $request->get('subtitle');
		$material->author = $request->get('author');
		$material->editor = $request->get('editor');
		$material->edition = $request->get('edition');
		$material->edition_year = $request->get('edition_year');
		$material->publisher = $request->get('publisher');
		$material->publishing_year = $request->get('publishing_year');
		$material->place_of_publication = $request->get('place_of_publication');
		$material->location = $request->get('location');
		$material->price = $request->get('price');
		$material->series = $request->get('series');
		$material->size = $request->get('size');
		$material->no_of_pages = $request->get('no_of_pages');
		$material->clue_page = $request->get('clue_page');
		$material->source_details = $request->get('source_details');
		$material->note = $request->get('note');
		$material->update();	

		//Delete + Insert = Update Msubject
		Msubject::whereMaterial_id($material_id)->delete();

		$subjects_id = [];
		$subjects = Department::get();

		if($subjects->count() > 0) :
			foreach($subjects as $subject) :
					$s_id = $subject->id;
					if($request->get($s_id) == $s_id) :
						$subjects_id[] = $s_id;
					endif;
			endforeach;		

			if(count($subjects_id) > 0) :
				foreach($subjects_id as $subject_id) :
						$msubject = new Msubject;
						$msubject->subject_id = $subject_id;
						$msubject->material_id = $material_id;
						$msubject->save();
				endforeach;
			endif;	
		endif;	

		//Page
		$count = Maccession::where('id', '<=', $id)->count();
		$page = ceil($count/Study::paginate());

		$message = 'You have successfully updated the material';
		return Redirect::route('library.material.index', ['page' => $page])
						->with('info',$message)
						->withId($material_id);			

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$id = $request->get('id');
		$maccession = Maccession::find($id); 
		$material_id = $maccession->material_id;
		$maccession->delete();

		$have_maccession = Maccession::whereMaterial_id($material_id)->count();

		if($have_maccession == 0) :
			Material::whereId($material_id)->delete();
			Msubject::whereMaterial_id($material_id)->delete();
		endif;	

		$error_message = 'You have deleted the material';
		return Redirect::back()->with('warning',$error_message);

	}



	public function search(Request $request) {

		$title = 'Easy CollegeMate - Library Material';
		$breadcrumb = 'library.material.index:Library Material|Dashboard';	
		
		//Search Material
		$physical_form = $request->get('physical_form');
		$accession_no = $request->get('accession_no');
		$call_no = $request->get('call_no');
		$book_title =$request->get('title');
		$author = $request->get('author');

		$maccessions = Study::searchMaterial($physical_form, $accession_no, $call_no, $book_title, $author);

		//Form Element
		$physical_form_lists = ['' => 'Select physical form','book' => 'Book','journal' => 'Journal','cd/dvd' => 'CD/DVD','manuscript' => 'Manuscript','others' => 'Others'];		

		return view('BackEnd.library.material.index', compact('title', 'breadcrumb', 'maccessions','physical_form_lists','physical_form','accession_no','call_no','book_title','author'));

	}



	public function upload() {

		$title = 'Easy CollegeMate - Upload Material From CSV';
		$breadcrumb = 'library.material.index:Library Material|Upload Material From CSV';

		return view('BackEnd.library.material.upload')
					->withTitle($title)
					->withBreadcrumb($breadcrumb);

	}



	public function postUpload(Request $request) {

		$data = $request->all();
		$validation = Material::uploadValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		$file = $request->file('material_csv');
		$extension = $file->getClientOriginalExtension();

		if(strtolower($extension) == 'csv') :	

			ini_set("auto_detect_line_endings", true);
			$tmp_file = $file->getRealPath();
			$handle = fopen($tmp_file, 'r');
			$row = 1;

			$material_ids = [];

			while(($fileop = fgetcsv($handle, 1000,",")) !== FALSE) : 
				if($row != 1) :
					if(count($fileop) == 22) :
						if($fileop[0] && $fileop[1] && $fileop[2] && $fileop[3] && $fileop[4] && $fileop[5] && $fileop[6] && $fileop[7] && $fileop[8] && $fileop[9] && $fileop[10] && $fileop[11] && $fileop[12] && $fileop[13] && $fileop[14] && $fileop[15] && $fileop[16] && $fileop[17] && $fileop[18] && $fileop[19] && $fileop[20] && $fileop[21]) :

							$accession_no = $fileop[0];
							$no_of_books = $fileop[1];
							$isbn = $fileop[2];
							$call_no = $fileop[3];
							$physical_form = $fileop[4];
							$title = $fileop[5];
							$subtitle = $fileop[6];
							$author = $fileop[7];
							$editor = $fileop[8];
							$edition = $fileop[9];
							$edition_year = $fileop[10];
							$publisher = $fileop[11];
							$publishing_year = $fileop[12];
							$publication_place = $fileop[13];
							$location = $fileop[14];
							$price = $fileop[15];
							$series = $fileop[16];
							$size = $fileop[17];
							$no_of_pages = $fileop[18];
							$clue_page = $fileop[19];
							$source_details = $fileop[20];
							$subjects = $fileop[21];

							//Accession Number checker
							$initial_accession_no = $accession_no;
							$no_of_books = $no_of_books;
							$final_accession_no = $initial_accession_no + $no_of_books - 1;

							foreach(range($initial_accession_no, $final_accession_no) as $index) :
								$accession_no_exists = Maccession::whereAccession_no($index)->count();
								if($accession_no_exists > 0) :
									if($row == 2) :							
										$error_message = 'The accession no ' . $index . ' in row no ' . $row . ' is already taken';
										return Redirect::back()->withInput()->with('error',$error_message);
									endif;

									if($row > 2) :							
										$error_message = 'The accession no ' . $index . ' in row no ' . $row . ' is already taken. Rows of before row ' . $row . ' are successfully inserted';

										//Page
										$page = ceil(Maccession::count()/Study::paginate());

										$id = $material_ids;

										$message = 'You have successfully created new library material';

										return Redirect::route('library.material.index',['page' => $page])
														->with('success',$message)
														->with('error',$error_message)
														->withId($id);	
									endif;								
								endif; 
							endforeach;		

							//Subject Checker
							$subject_ids = [];
							if(strpos($subjects,',') !== FALSE) :
								$subject_array = explode(',',$subjects);
								foreach($subject_array as $subject) :
									$subject_exist = Subject::whereName($subject)->count();
									if($subject_exist > 0) :
										$subject_id = Subject::whereName($subject)->pluck('id')->first();
										$subject_ids[] = $subject_id;
									else :
										if($row > 2) :
											$error_message = 'There is invalid subject entry in row no ' . $row . '. Rows of before row ' . $row . ' are successfully inserted';
											
											//Page
											$page = ceil(Maccession::count()/Study::paginate());

											$id = $material_ids;

											$message = 'You have successfully created new library material';

											return Redirect::route('library.material.index',['page' => $page])
															->with('success',$message)
															->with('error',$error_message)
															->withId($id);	
										endif;
										
										if($row == 2) :
											$error_message = 'There is invalid subject entry in row no ' . $row;
											return Redirect::back()->withInput()->with('error',$error_message);
										endif;	
									endif;
								endforeach;	
							else :
								$subject = $subjects;
								$subject_exist = Subject::whereName($subject)->count();
								if($subject_exist > 0) :
									$subject_id = Subject::whereName($subject)->pluck('id')->first();
									$subject_ids[] = $subject_id;
								else :
									if($row > 2) :
										$error_message = 'There is invalid subject entry in row no ' . $row . '. Rows of before row ' . $row . ' is successfully inserted';
										
										//Page
										$page = ceil(Maccession::count()/Study::paginate());

										$id = $material_ids;

										$message = 'You have successfully created new library material';

										return Redirect::route('library.material.index',['page' => $page])
														->with('success',$message)
														->with('error',$error_message)
														->withId($id);	
									endif;
									
									if($row == 2) :
										$error_message = 'There is invalid subject entry in row no ' . $row;
										return Redirect::back()->withInput()->with('error',$error_message);
									endif;	
								endif;
							endif;	

							//Insert Material
							$material = new Material;
							$material->isbn = $isbn;
							$material->call_no = $call_no;
							$material->physical_form = $physical_form;
							$material->title = $title;
							$material->subtitle = $subtitle;
							$material->author = $author;
							$material->editor = $editor;
							$material->edition = $edition;
							$material->edition_year = $edition_year;
							$material->publisher = $publisher;
							$material->publishing_year = $publishing_year;
							$material->place_of_publication = $publication_place;
							$material->location = $location;
							$material->price = $price;
							$material->series = $series;
							$material->size = $size;
							$material->no_of_pages = $no_of_pages;
							$material->clue_page = $clue_page;
							$material->no_of_books = $no_of_books;
							$material->source_details = $source_details;
							$material->save();

							$material_id = $material->id;
							$material_ids[] = $material_id;

							//Insert Maccession
							foreach(range($initial_accession_no,$final_accession_no) as $index) :

								$maccession = new Maccession;
								$maccession->material_id = $material_id;
								$maccession->accession_no = $index;
								$maccession->save();

							endforeach;		

							//Insert Msubject
							if(count($subject_ids) > 0) :
								foreach($subject_ids as $subject_id) :
										$msubject = new Msubject;
										$msubject->subject_id = $subject_id;
										$msubject->material_id = $material_id;
										$msubject->save();
								endforeach;
							endif;												

						endif;	
					else :
						$error_message = 'Invalid CSV data format';
						return Redirect::back()->with('error',$error_message);
					endif;	
				endif;
				$row++;
			endwhile;

			//Page
			$page = ceil(Maccession::count()/Study::paginate());

			$id = $material_ids;

			$message = 'You have successfully created new library material';

			return Redirect::route('library.material.index',['page' => $page])
							->with('success',$message)
							->withId($id);			

		else :

			$error_message = 'The material csv must be a file of type: csv';
			return Redirect::back()->with('error',$error_message);
		
		endif;	

	}



	public function csv() {

		return Response::download(public_path() .'/csv/library-material.csv');

	}

	public function material_catalog(Request $request){
		$materials = Material::where('id',$request->id)->get();

		return view('BackEnd.library.material.material_catalog', compact('materials'));
	}

	public function material_details(Request $request){
		$material = Material::find($request->id);

		return view('BackEnd.library.material.material_details', compact('material'));
	}
}
