<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\PayslipGenerator;
use App\Models\PayslipHeader;
use App\Models\PayslipItem;
use App\Models\PayslipTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use Session;
use DB;

class AdminPayslipGeneratorController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:payslip_generator.manage');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        Session::forget('payslip_title');
        Session::forget('status');

        $title = 'Easy CollegeMate - PaySlip Generator';
        $breadcrumb = 'admin.admission.index:Admission Management|admin.payslip_generator.index:PaySlip Generator|Dashboard';
        $payslip_titles = PayslipTitle::paginate(Study::paginate());

        //search filter form data
        $status_lists = ['' => 'Select Status', 1 => 'Listed', 0 => 'Not Listed'];

        return view('BackEnd.admin.payslip_generator.index', compact('title', 'breadcrumb', 'payslip_titles','status_lists'));

    }



    public function create() {
        $header_id = request()->get('header_id');
        $data = [
            'title' => '',
            'types' => [],
            'type' => '',
            'header' => PayslipHeader::find($header_id),
            'items' => PayslipItem::where('payslipheader_id', $header_id)->get(),
            'title_options' => PayslipTitle::where('payslipheader_id', $header_id)->pluck('title', 'id')->toArray(),
        ];

        $html =  view('BackEnd.admin.payslip_generator.particles.form', $data)->render();

        return response()->json([
            'status' => 202,
            'html' => $html
        ],Response::HTTP_OK);

    }



    public function store(Request $request) {
        DB::beginTransaction();

        try {

            $id = $request->id;

            $action_type = $request->action_type;

            if($action_type == 'update'){
                $msg = 'Payslip Generator Updated Successfully';
                return $this->update($request);
            }else{
                $msg = 'Payslip Generator Created Successfully';
            }

            $data = $request->all();
            $validation = PayslipTitle::validate($data);

            if($validation->fails()) :
                return response()->json(['errors'=>$validation->errors()], 422);
            endif;

            //Insert PaySlip Generator With PaySlip Title Id, Header Id, Item Id & Fees
            $payslipitem_ids = [];

            $payslipitems = PayslipItem::get();

            if($payslipitems->count() > 0) :

                foreach($payslipitems as $payslipitem) :

                    $payslip_item_id = $payslipitem->id;

                    if($request->get($payslip_item_id) == $payslip_item_id) :

                        $payslipitem_ids[] = $payslip_item_id;

                        $fee = $request->get('fee' . $payslip_item_id);

                        if(!is_numeric($fee)) :
                            $error_message = 'Invalid fee amount';
                            return response()->json([
                                'error' => $error_message
                            ],Response::HTTP_NOT_ACCEPTABLE);

                        endif;  

                    endif;  

                endforeach;

                if(count($payslipitem_ids) > 0) :
                    $generator_ids = [];

                    //Insert PaySlip Title & Catch PaySlip ID
                    $payslip_title = PayslipTitle::find($request->title);

                    $paysliptitle_id = $payslip_title->id;              

                    foreach($payslipitem_ids as $payslipitem_id) :

                        $payslipheader_id = PayslipItem::whereId($payslipitem_id)->pluck('payslipheader_id');
                        $fees = $request->get('fee' . $payslipitem_id);

                        $payslip_generator = new PayslipGenerator;
                        $payslip_generator->paysliptitle_id = $paysliptitle_id;
                        $payslip_generator->payslipheader_id = $payslipheader_id[0];
                        $payslip_generator->payslipitem_id = $payslipitem_id;
                        $payslip_generator->fees = $fees;
                        $payslip_generator->save();
                        $generator_ids[] = $payslip_generator->id;

                    endforeach;

                DB::commit();

                else :
                    
                    $error_message = 'You have to add at least one fee item to generate a new payslip';
                    return response()->json([
                        'error' => $error_message
                    ],Response::HTTP_NOT_ACCEPTABLE);

                endif;  

            else :

                $error_message = 'You have to add at least one fee item to generate a new payslip';
                return response()->json([
                    'error' => $error_message
                ],Response::HTTP_NOT_ACCEPTABLE);

            endif;

            $generators = PayslipGenerator::whereIn('id', $generator_ids)->orderBy('id', 'desc')->get();

            $html = view('BackEnd.admin.payslip_generator.particles.tableRow', compact('payslip_title','generators'))->render();

            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'id' => $payslip_title->id,
                'table' => 'table_generator',
                'modal'=> 'ajax-modal-lg',
                'html' => $html
            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }    

    }



    public function show($id) {

        $payslip_title = PayslipTitle::find($id);
        $breadcrumb_title = substr($payslip_title->title, 0, 50);
        $title = 'Easy CollegeMate - PaySlip Generator';
        $breadcrumb = 'admin.admission.index:Admission Management|admin.payslip_generator.index:PaySlip Generator|' . $breadcrumb_title;

        //get payslip headers associated with the payslip title
        $headers_array = [];
        $headers = PayslipGenerator::wherePaysliptitle_id($id)->groupBy('payslipheader_id')->get();
    
        foreach($headers as $header) :
            $headers_array[] = $header->payslipheader_id . '<br>';
        endforeach; 

        //get total fees
        $total_fees = PayslipGenerator::wherePaysliptitle_id($payslip_title->id)->sum('fees');

        //get pagination page
        $count = Paysliptitle::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate());

        return view('BackEnd.admin.payslip_generator.show', compact('title', 'breadcrumb','payslip_title','headers_array','total_fees','page'));      

    }



    public function edit($id) {

        $payslip_generator = PayslipGenerator::find($id);
        if(is_null($payslip_generator)){
            $error_message = 'Something is missing. Please Refresh The Page First';
                return response()->json([
                    'error' => $error_message,
                    'status' => 'warning'
                ],Response::HTTP_NOT_ACCEPTABLE);
        }

        $header_id = $payslip_generator->payslipheader_id;
        $data = [
            'generator' => $payslip_generator,
            'header' => PayslipHeader::find($header_id)
        ];

        $html =  view('BackEnd.admin.payslip_generator.particles.form-edit', $data)->render();

        return response()->json([
                'status' => 202,
                'html' => $html

            ],Response::HTTP_OK);

    }



    public function update(Request $request) {
        $id = $request->id;
        $msg = 'Payslip Generator Updated Successfully';
        $generator = PayslipGenerator::find($id);
        $generator->fees = $request->fees;
        $generator->save();

        $generators = PayslipGenerator::where('id',$id)->get();

        $html = view('BackEnd.admin.payslip_generator.particles.tableRow', compact('generators'))->render();

        DB::commit();

        return response()->json([
            'status' => 'info',
            'message' => $msg,
            'id' => $generator->id,
            'table' => 'table_generator',
            'modal'=> 'ajax-modal-lg',
            'html' => $html

        ],Response::HTTP_OK);
                 

    }



    public function destroy(Request $request, $id) {
        try {
            $generator = PayslipGenerator::where('id',$id)->get();
            if(count($generator) > 0){
                PayslipGenerator::find($generator[0]->id)->delete();
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Payslip Generator Deleted Successfully',
                    'id' => $id,
                    'table' => 'table_generator',

                ],Response::HTTP_OK);
            }

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }   

    }



    public function status($id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('message',$error_message);
        endif;

        $status = $request->get('status');

        if($status != 1 && $status != 0) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('message',$error_message);
        endif;      
        
        $payslip_title = PayslipTitle::find($id);
        $payslip_title->status = $status;
        $payslip_title->update();

        if($status == 1) :
            $message = 'You have listed the payslip';
            return Redirect::back()->withMessage($message);
        else :
            $error_message = 'You have unlisted the payslip';
            return Redirect::back()->with('message',$error_message);     
        endif;              

    }



    public function search(Request $request) {
        Session::forget('payslip_title');
        Session::forget('status');

        $title = 'Easy CollegeMate - PaySlip Generator';
        $breadcrumb = 'admin.admission.index:Admission Management|admin.payslip_generator.index:PaySlip Generator|Dashboard';
        
        //search payslip titles outcomes
        $payslip_title = Study::filterInput('title', $request->get('title'));
        $status = Study::filterInput('status', $request->get('status'));

        Session::put('payslip_title', $payslip_title); 
        Session::put('status', $status); 

        $payslip_titles = Study::searchPayslipTitle($payslip_title, $status);

        //search filter form data
        $status_lists = ['' => 'Select Status', 1 => 'Listed', 0 => 'Not Listed'];

        return view('BackEnd.admin.payslip_generator.index', compact('title','breadcrumb', 'payslip_titles', 'status_lists', 'payslip_title', 'status'));

    }
}
