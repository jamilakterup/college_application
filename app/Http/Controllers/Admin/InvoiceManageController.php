<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use DB;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Validator;
use App\Libs\Study;
class InvoiceManageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Easy CollegeMate - Invoice Management';
        $breadcrumb = 'admin.invoice.index:Invoice List|Dashboard';

        return view('BackEnd.admin.management.invoice.index', compact('title', 'breadcrumb'));
    }


    public function datasource(Request $request){
        $invoices = Invoice::query();

        return Datatables::of($invoices)
                ->addColumn('actions', function ($invoices) {
                    $html = " <a href=".route('admin.invoice.show', $invoices->id)." class='btn btn-info type-b show_data' data-id=".$invoices->id." data-label='Invoice'><i class='fas fa-eye'></i></a>";
                    $html .= " <a href=".route('admin.invoice.edit', $invoices->id)." class='btn btn-primary type-b duplicate_data' data-id=".$invoices->id." data-label='Invoice'><i class='fas fa-copy'></i></a>";
                    $html .= " <a href=".route('admin.invoice.edit', $invoices->id)." class='btn btn-primary type-b edit_data' data-id=".$invoices->id." data-label='Invoice'><i class='fas fa-pencil'></i></a>";
                    $html .= " <a href=".route('admin.invoice.destroy', $invoices->id)." class='btn btn-danger type-b delete_data' data-id=".$invoices->id."><i class='fas fa-trash'></i></a>";
                    return $html;
                })
                ->addColumn('operations', function ($invoices) {
                    return '<a class="btn btn-sm btn-primary invoice_generate" data-action="Generating" href="'.route('invoice.generate', ['payslipinvoice_id' => $invoices->id]).'">Generate Bill</a>';
                })
                ->addColumn('checkbox', function($invoices){
                      return '<input type="checkbox" name="item_checkbox" data-id="'.$invoices->id.'"><label></label>';
                  })
                ->filter(function ($query) use ($request) {

                    if ($request->has('roll') && ! is_null($request->get('roll'))) {
                        $query->where('roll', $request->get('roll'));
                    }

                    if ($request->has('pro_group') && ! is_null($request->get('pro_group'))) {
                        $query->where('pro_group', $request->get('pro_group'));
                    }

                    if ($request->has('subject') && ! is_null($request->get('subject'))) {
                        $query->where('subject', $request->get('subject'));
                    }

                    if ($request->has('level') && ! is_null($request->get('level'))) {
                        $query->where('level', $request->get('level'));
                    }

                    if ($request->has('session') && ! is_null($request->get('session'))) {
                        $query->where('admission_session', $request->get('session'));
                    }

                    if ($request->has('exam_year') && ! is_null($request->get('exam_year'))) {
                        $query->where('passing_year', $request->get('exam_year'));
                    }

                    if ($request->has('type') && ! is_null($request->get('type'))) {
                        $query->where('type', $request->get('type'));
                    }

                    $query->where('status', 'Pending');
                })
                
                ->setRowAttr([
                    'data-row-id' => function($invoices) {
                        return $invoices->id;
                    },
                    'class'=> function($invoices) {
                        return 'text-center ' . Study::updatedRow('id', $invoices->id);
                    }
                ])
                ->rawColumns(['actions', 'checkbox'])
                ->escapeColumns(['checkbox', 'details'])
                ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $html =  view('BackEnd.admin.management.invoice.form')->render();

        return response()->json([
                'status' => 202,
                'modal' => 'modal-lg',
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
        DB::beginTransaction();

        try {

            $id = $request->id;

            $action_type = $request->action_type;

            if($action_type == 'update'){
                $invoice = Invoice::where('id',$id)->where('status', 'Pending')->first();
                $msg = 'Invoice Updated Successfully';
                $status = 'info';
            }else{
                $invoice = new Invoice;
                $msg = 'Invoice Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $pro_group = $request->get('pro_group');
            $subject = $request->subject ?? $request->get('pro_group');
            $roll = $request->roll;
            $type = $request->get('type');
            $level = $request->get('level');
            $session = $request->get('session');
            $exam_year = $request->get('exam_year');
            $student_type = $request->get('student_type') ?? 'general';
            $registration_type = $request->registration_type;
            $slip_name = $request->slip_name;
            $pay_type = $request->pay_type;
            $total_papers = default_zero($request->get('total_papers'));
            $total_amount = $request->get('total_amount');

            $rules[] = Invoice::validateRules($data);

            $this->validate($request, Arr::collapse($rules));

            $invoice->name = $request->get('name');
            $invoice->roll = $request->get('roll');
            $invoice->date_start = $request->get('start_date');
            $invoice->date_end = $request->get('end_date');
            $invoice->pro_group = $pro_group;
            $invoice->subject = $subject;
            $invoice->slip_name = $slip_name;
            $invoice->type = $type;
            $invoice->level = $level;
            $invoice->admission_session = $session;
            $invoice->passing_year = $exam_year;
            $invoice->student_type = $student_type;
            $invoice->registration_type = $registration_type;
            $invoice->pay_type = $pay_type;
            $invoice->total_papers = $total_papers;
            $invoice->total_amount = $total_amount;
            $invoice->status = 'Pending';
            $invoice->institute_code = INS_CODE;

            $invoice->save();

            DB::commit();

            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $invoice->id

            ],Response::HTTP_OK);
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
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
        $invoice = Invoice::find($id);

        $html =  view('BackEnd.admin.management.invoice.show', compact('invoice'))->render();

        return response()->json([
                'status' => 202,
                'modal' => 'modal-lg',
                'html' => $html
        ],Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = Invoice::find($id);
        $data = [
            'name' => $invoice->name,
            'roll' => $invoice->roll,
            'total_papers' => $invoice->total_papers,
            'slip_name' => $invoice->slip_name,
            'start_date' => $invoice->date_start,
            'end_date' => $invoice->date_end,
            'level' => $invoice->level,
            'pro_group' => $invoice->pro_group,
            'subject' => $invoice->subject,
            'session' => $invoice->admission_session,
            'exam_year' => $invoice->passing_year,
            'student_type' => $invoice->student_type,
            'registration_type' => $invoice->registration_type,
            'pay_type' => $invoice->pay_type,
            'type' => $invoice->type,
            'total_amount' => $invoice->total_amount,
        ];

        $html = view('BackEnd.admin.management.invoice.form', $data)->render();
        return response()->json([
                'status' => 200,
                'modal' => 'modal-lg',
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
            Invoice::find($id)->delete();
            return response()->json([
                'status' => 'warning',
                'message' => 'Invoice Deleted Successfully',
                'id' => $id,
                'table' => 'datatable',

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteAll(Request $request){
        DB::beginTransaction();
        try {
            foreach ($request->ids as $id) {
                Invoice::find($id)->delete();
            }

            DB::commit();
            return response()->json([
                'status' => 'warning',
                'message' => 'Selected Invoices Deleted Successfully',
                'id' => $id,
                'table' => 'datatable',
            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }
}
