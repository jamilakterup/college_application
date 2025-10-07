<?php

namespace App\Http\Controllers\Hall;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class HallController extends Controller
{
    public function index() {
        $hallinfo = DB::table('hostel_name')->get();
		return view('BackEnd.hall.hall.index',compact('hallinfo'));

	}

	public function create(){
		return view('BackEnd.hall.hall.create');
	}

	public function store(Request $request){
		$this->validate($request, [
			'hostel_name' => 'required',
			'total_seat' => 'required',
			'available_seat' => 'required',
			'no_room' => 'required',
			'provost' => 'required'
		]);

		$hall_name=$request->hostel_name;
		$total_seat=$request->total_seat;
		$available_seat=$request->available_seat;
		$no_room=$request->no_room;
		$provost=$request->provost;

		DB::table('hostel_name')->insert(
	    ['hostel_name'=> $hall_name,'total_seat'=> $total_seat,'available_seat'=> $available_seat,'no_room'=> $no_room,'provost'=> $provost]);

	    return redirect()->route('hall.index')->with('success' ,'Hall Added Successfully.');


	}

	public function show($id){

	}

	public function edit($id){
		$hostel = DB::table('hostel_name')->where('id', $id)->first();
	    return view('BackEnd.hall.hall.edit', compact('hostel'))
	                    ->withId($id);
	}

	public function update(Request $request ,$id){

		$this->validate($request, [
			'hostel_name' => 'required',
			'total_seat' => 'required',
			'available_seat' => 'required',
			'no_room' => 'required',
			'provost' => 'required'
		]);


		$hall_name=$request->hostel_name;
		$total_seat=$request->total_seat;
		$available_seat=$request->available_seat;
		$no_room=$request->no_room;
		$provost=$request->provost;

	    DB::table('hostel_name')
	            ->where('id', $id)  
	            ->limit(1)  // optional - to ensure only one record is updated.
	            ->update(array('hostel_name'=> $hall_name,'total_seat'=> $total_seat,'available_seat'=> $available_seat,'no_room'=> $no_room,'provost'=> $provost));
	    return redirect()->route('hall.index')->with('info' ,'Hall information is updated successfully.');
	}

	public function addHall(){
		$hallinfo = DB::table('hostel_name')->get();

	    return view('hall.add_hall', compact('hallinfo'));

	}

	public function hallAddInput(Request $request){
		$hall_name=htmlspecialchars($_POST['hall_name']);
		$total_seat=htmlspecialchars($_POST['total_seat']);
		$available_seat=htmlspecialchars($_POST['available_seat']);
		$no_room=htmlspecialchars($_POST['no_room']);
		$provost=htmlspecialchars($_POST['provost']);
		DB::table('hostel_name')->insert(
	    ['hostel_name'=> $hall_name,'total_seat'=> $total_seat,'available_seat'=> $available_seat,'no_room'=> $no_room,'provost'=> $provost]);	

	    echo "Hall information is inserted successfully";
	}

	public function editHall(){
		  $id=Input::get('id');

	      return view('hall.edit_hall')
	                    ->withId($id);
	}

	public function hallEditInput(){
		$id = htmlspecialchars($_POST['hallid']);
		$hall_name=htmlspecialchars($_POST['hall_name']);
		$total_seat=htmlspecialchars($_POST['total_seat']);
		$available_seat=htmlspecialchars($_POST['available_seat']);
		$no_room=htmlspecialchars($_POST['no_room']);
		$provost=htmlspecialchars($_POST['provost']);

	    DB::table('hostel_name')
	            ->where('id', $id)  
	            ->limit(1)  // optional - to ensure only one record is updated.
	            ->update(array('hostel_name'=> $hall_name,'total_seat'=> $total_seat,'available_seat'=> $available_seat,'no_room'=> $no_room,'provost'=> $provost));
	            echo "Hall information is updated successfully";    	
	}

	public function allocateSeat(){
			$seatinfo = DB::table('hostel_allocation')->get();

		     return view('hall.new_seat_allocation')->withSeatinfo($seatinfo);

	}

	public function hallAllocationRoomSelect(){
	    if(Request::ajax())
	      {	
	      	 $hall_name=Input::get('hall_name'); 
	        echo "<input type='hidden' id='hidden_hall_name' value='{$hall_name}' />";
	      	 $result = DB::table('hostel_description')
	        ->select('room_no')
	        ->Where('hostel_name', $hall_name)
	        ->get();
	        echo "<select id='room_no'>";
	           foreach ($result as  $value)
	            {                          

	              echo  "<option value='{$value->room_no}'>{$value->room_no}</option>";               
	            }         
	      	echo "</select>";
	      }
	}

	public function hallSeatAllocationselect(){
	    if(Request::ajax())
	      {	

	      	$hall_name=htmlspecialchars(Input::get('hall_name'));
	         $room_no=htmlspecialchars(Input::get('room_no'));
	      	 $result = DB::table('hostel_seat_description')
	        ->select('seat_no')
	        ->Where('hostel_name', $hall_name)->Where('room_no', $room_no)->Where('status','available')->get();
	          echo "<select id='seat_no'>";
	           foreach ($result as  $value)
	            {                          

	              echo  "<option value='{$value->seat_no}'>{$value->seat_no}</option>";               
	            }         
	      	echo "</select>";          
	         

	      }



	}

	public function seatAllocationInput(){

	        return view('hall.new_seat_allocation_input_action');

	} 

	public function detailsSeatAllocation(){
	     $id=Input::get('id');
	      return view('hall.details_new_seat_allocation')
	                    ->withId($id);
	}

	public function editSeatAllocation(){
		     $id=Input::get('id');
	      return view('hall.edit_new_seat_allocation')
	                    ->withId($id);
	}


	public function hallAllocationRoomSelect2(){
	    if(Request::ajax())
	      {	
	      	 $hall_name=Input::get('hall_name'); 
	        echo "<input type='hidden' id='hidden_hall_name2' value='{$hall_name}' />";
	      	 $result = DB::table('hostel_description')
	        ->select('room_no')
	        ->Where('hostel_name', $hall_name)
	        ->get();
	        echo "<select id='room_no2'>";
	           foreach ($result as  $value)
	            {                          

	              echo  "<option value='{$value->room_no}'>{$value->room_no}</option>";               
	            }         
	      	echo "</select>";
	      }
	}

	public function hallAllocationSeatSelect2(){
	    if(Request::ajax())
	      {	

	      	$hall_name=htmlspecialchars(Input::get('hall_name'));
	         $room_no=htmlspecialchars(Input::get('room_no'));
	      	 $result = DB::table('hostel_seat_description')
	        ->select('seat_no')
	        ->Where('hostel_name', $hall_name)->Where('room_no', $room_no)->Where('status','available')->get();
	          echo "<select id='seat_no2'>";
	           foreach ($result as  $value)
	            {                          

	              echo  "<option value='{$value->seat_no}'>{$value->seat_no}</option>";               
	            }         
	      	echo "</select>";          
	         

	      }

	}
	public function editSeatAllocationInput(){

	        return view('hall.edit_new_seat_allocation_input');

	} 

	public function deleteSeatAllocation(){
		$id=Input::get('id');
	    DB::table('hostel_allocation')->where('id', $id)->delete();
	}
}
