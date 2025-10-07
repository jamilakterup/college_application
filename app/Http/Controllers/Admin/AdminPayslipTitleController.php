<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayslipGenerator;
use App\Models\PayslipHeader;
use App\Models\PayslipItem;
use App\Models\PayslipTitle;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class AdminPayslipTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
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
            'header' => PayslipHeader::find($header_id)
        ];

        $html =  view('BackEnd.admin.payslip_title.particles.form', $data)->render();

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
                $payslip_title = PayslipTitle::find($id);
                $msg = 'PayslipTitle Updated Successfully';
                $status = 'info';
            }else{
                $payslip_title = new PayslipTitle;
                $msg = 'PayslipTitle Created Successfully';
                $status = 'success';
            }


            $data = $request->all();
            if($action_type == 'update')
            $validation = PayslipTitle::updateValidate($data);
            else
            $validation = PayslipTitle::validate($data);

            if($validation->fails()) :
                return response()->json(['errors'=>$validation->errors()], 422);
            endif;

            $payslip_title->title = $request->get('title');
            $payslip_title->payslipheader_id = $request->get('payslipheader_id');
            $payslip_title->save();

            $id = $payslip_title->id;

            $html = view('BackEnd.admin.payslip_title.particles.tableRow', compact('payslip_title'))->render();

            return response()->json([
                'status' => $status,
                'message' => $msg,
                'id' => $payslip_title->id,
                'table' => 'table_title',
                'modal'=> 'ajax-modal-lg',
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $payslip_title = PayslipTitle::find($id);

        $header_id = $payslip_title->payslipheader_id;
        $data = [
            'title' => $payslip_title->title,
            'header' => PayslipHeader::find($header_id)
        ];

        $html =  view('BackEnd.admin.payslip_title.particles.form', $data)->render();

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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $titles = PayslipTitle::where('id',$id)->get();
            if(count($titles)){
                $generators = PayslipGenerator::where('paysliptitle_id', $titles[0]->id)->get();
                foreach($generators as $generator){
                    PayslipGenerator::find($generator->id)->delete();
                }

                PayslipTitle::find($id)->delete();
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Payslip Title Deleted Successfully',
                    'id' => $id,
                    'table' => 'table_title',

                ],Response::HTTP_OK);
            }

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }
}
