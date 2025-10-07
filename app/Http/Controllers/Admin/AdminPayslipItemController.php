<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\PayslipGenerator;
use App\Models\PayslipHeader;
use App\Models\PayslipItem;
use App\Models\PayslipTitle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class AdminPayslipItemController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:payslip_item.manage');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Easy CollegeMate - PaySlip Item';
        $breadcrumb = 'admin.admission.index:Admission Management|admin.payslip_item.index:PaySlip Item|Dashboard';
        $payslip_items = PayslipItem::paginate(Study::paginate());

        //search filter form data
        $payslip_headers_list = selective_multiple_payslip_header();

        return view('BackEnd.admin.payslip_item.index', compact('payslip_items', 'payslip_headers_list'))
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $header_id = request()->get('header_id');
        $data = [
            'title' => '',
            'types' => [],
            'type' => '',
            'header' => PayslipHeader::find($header_id)
        ];

        $html =  view('BackEnd.admin.payslip_item.particles.form', $data)->render();

        return response()->json([
            'status' => 202,
            'html' => $html

        ],Response::HTTP_OK);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $id = $request->id;

            $action_type = $request->action_type;

            if($action_type == 'update'){
                $payslip_item = PayslipItem::find($id);
                $msg = 'PayslipItem Updated Successfully';
                $status = 'info';
            }else{
                $payslip_item = new PayslipItem;
                $msg = 'PayslipItem Created Successfully';
                $status = 'success';
            }


            $data = $request->all();
            $validation = PayslipItem::validate($data);

            if($validation->fails()) :
                return response()->json(['errors'=>$validation->errors()], 422);
            endif;  

            $payslip_item->payslipheader_id = $request->get('payslipheader_id');
            $payslip_item->item = $request->get('item');
            $payslip_item->type_id = $request->get('item_type');
            $payslip_item->save();

            $id = $payslip_item->id;

            $html = view('BackEnd.admin.payslip_item.particles.tableRow', compact('payslip_item'))->render();

            return response()->json([
                'status' => $status,
                'message' => $msg,
                'id' => $payslip_item->id,
                'table' => 'table_item',
                'modal'=> 'modal-lg',
                'html' => $html

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Easy CollegeMate - PaySlip Item';
        $breadcrumb = 'admin.admission.index:Admission Management|admin.payslip_item.index:PaySlip Item';
        $payslip_item = PayslipItem::find($id);

        return View::make('admin.payslip_item.show', compact('payslip_item'))
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payslip_item = PayslipItem::find($id);

        $header_id = $payslip_item->payslipheader_id;
        $data = [
            'title' => $payslip_item->item,
            'type' => $payslip_item->type_id,
            'header' => PayslipHeader::find($header_id)
        ];

        $html =  view('BackEnd.admin.payslip_item.particles.form', $data)->render();

        return response()->json([
                'status' => 202,
                'html' => $html

            ],Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->withError_message($error_message);
        endif;

        $data = $request->all();
        $validation = PayslipItem::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        $payslip_item = PayslipItem::find($id);
        $payslip_item->payslipheader_id = $request->get('payslipheader_id');
        $payslip_item->item = $request->get('item');
        $payslip_item->update();

        $count = PayslipItem::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate());

        $message = 'You have successfully updated the paysleep item';
        return Redirect::route('admin.payslip_item.index', ['page' => $page])
                        ->with('success',$message)
                        ->withId($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $generators = PayslipGenerator::where('payslipitem_id', $id)->get();
            foreach($generators as $generator){
                $title_id = $generator->paysliptitle_id;
                PayslipGenerator::find($generator->id)->delete();
                $titles = PayslipTitle::where('id',$title_id)->get();
                if(count($titles) > 0){
                    PayslipTitle::find($title_id)->delete();
                }
            }
            PayslipItem::find($id)->delete();

            return response()->json([
                'status' => 'warning',
                'message' => 'Payslip Item Deleted Successfully',
                'id' => $id,
                'table' => 'table_item',

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    public function search(Request $request) {

        $title = 'Easy CollegeMate - PaySlip Item';
        $breadcrumb = 'admin.admission.index:Admission Management|admin.payslip_item.index:PaySlip Item|Dashboard';

        //search payslip items outcomes
        $payslipheader_id = Study::filterInput('payslipheader_id', $request->get('payslipheader_id'));

        $payslip_items = Study::searchPayslipItem($payslipheader_id);

        //search filter form data
        $payslip_headers_list = selective_multiple_payslip_header();

        return view('BackEnd.admin.payslip_item.search', compact('payslip_items', 'payslip_headers_list', 'payslipheader_id'))
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb);

    }
}
