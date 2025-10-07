<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Libs\Study;
use App\Models\PayslipGenerator;
use App\Models\PayslipHeader;
use App\Models\PayslipItem;
use App\Models\PayslipTitle;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Validator;
use Illuminate\Support\Arr;

class AdminPayslipHeaderController extends Controller
{
    function __construct()
    {
         // $this->middleware(
         //    'permission:payslipheader.index|payslipheader.create|payslipheader.edit|payslipheader.delete', ['only' => ['index','show']]);
         // $this->middleware('permission:product-create', ['only' => ['create','store']]);
         // $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
         // $this->middleware('permission:product-delete', ['only' => ['destroy']]);

         $this->middleware('permission:payslip_header.manage');
    }

    public function datasource(Request $request){
        $header = PayslipHeader::query();

        return Datatables::of($header)
                ->addColumn('actions', function ($header) {
                    $html = "<a href=".route('admin.payslip_header.field', $header->id)." class='btn btn-warning type-b show_data' data-id=".$header->id." data-label='Payslip Header'><i class='fas fa-angle-double-right'></i></a>";
                    $html .= " <a href=".route('admin.payslip_header.show', $header->id)." class='btn btn-info type-b show_data' data-id=".$header->id." data-label='Payslip Header'><i class='fas fa-eye'></i></a>";
                    $html .= " <a href=".route('admin.payslip_header.edit', $header->id)." class='btn btn-primary type-b duplicate_data' data-id=".$header->id." data-label='Payslip Header'><i class='fas fa-copy'></i></a>";
                    $html .= " <a href=".route('admin.payslip_header.edit', $header->id)." class='btn btn-primary type-b edit_data' data-id=".$header->id."  data-label='Payslip Header'><i class='fas fa-pencil'></i></a>";
                    $html .= " <a href=".route('admin.payslip_header.destroy', $header->id)." class='btn btn-danger type-b delete_data' data-id=".$header->id." data-label='Payslip Header'><i class='fas fa-trash'></i></a>";
                    return $html;
                })
                ->addColumn('operations', function ($header) {
                    return '<a class="btn btn-sm btn-primary invoice_generate" data-action="Generating" href="'.route('invoice.generate', ['payslipheader_id' => $header->id]).'">Generate Bill</a>';
                })

                ->filter(function ($query) use ($request) {

                    if ($request->has('pro_group') && ! is_null($request->get('pro_group'))) {
                        $query->where('pro_group', $request->get('pro_group'));
                    }

                    if ($request->has('group_dept') && ! is_null($request->get('group_dept'))) {
                        $query->where('group_dept', $request->get('group_dept'));
                    }

                    if ($request->has('subject') && ! is_null($request->get('subject'))) {
                        $query->where('subject', $request->get('subject'));
                    }

                    if ($request->has('level') && ! is_null($request->get('level'))) {
                        $query->where('level', $request->get('level'));
                    }

                    if ($request->has('session') && ! is_null($request->get('session'))) {
                        $query->where('session', $request->get('session'));
                    }

                    if ($request->has('exam_year') && ! is_null($request->get('exam_year'))) {
                        $query->where('exam_year', $request->get('exam_year'));
                    }

                    if ($request->has('type') && ! is_null($request->get('type'))) {
                        $query->where('type', $request->get('type'));
                    }
                })
                
                ->setRowAttr([
                    'data-row-id' => function($header) {
                        return $header->id;
                    },
                    'class'=> function($header) {
                        return 'text-center ' . Study::updatedRow('id', $header->id);
                    }
                ])
                ->rawColumns(['actions', 'operations'])
                // ->escapeColumns([])
                ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Easy CollegeMate - PaySlip Header';
        $breadcrumb = 'admin.admission.index:Admission Management|admin.payslip_header.index:PaySlip Header|Dashboard';
        $payslip_headers = PayslipHeader::paginate(Study::paginate());

        return view('BackEnd.admin.payslip_header.index', compact('title', 'breadcrumb','payslip_headers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $html =  view('BackEnd.admin.payslip_header.particles.form')->render();

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
                $payslip_header = PayslipHeader::find($id);
                $msg = 'PayslipHeader Updated Successfully';
                $status = 'info';
            }else{
                $payslip_header = new PayslipHeader;
                $msg = 'PayslipHeader Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $pro_group = default_zero($request->get('pro_group'));
            $group_dept = $request->group_dept != '' ? implode('_',$request->get('group_dept')): 0;
            $subject = default_zero($request->get('subject'));
            $type = default_zero($request->get('type'));
            $level = default_zero($request->get('level'));
            $session = default_zero($request->get('session'));
            $exam_year = default_zero($request->get('exam_year'));
            $student_type = default_zero($request->get('student_type'));
            $formfillup_type = $request->formfillup_type !='' ? implode('_',$request->formfillup_type) : 0;
            $session = $request->session != '' ? implode('_',$request->session) : 0;
            $subject = $request->subject != '' ? implode('_',$request->subject) : 0;
            $total_papers = default_zero($request->get('total_papers'));

            if ($type == 'application') {
                if ($session != 0 && count($request->session) > 1) {
                    return response()->json([
                        'error' => 'Please Select Single Session for Application'
                    ],Response::HTTP_BAD_REQUEST);
                }
            }

            if($action_type == 'update')
            $rules[] = PayslipHeader::updateValidateRules($data);
            else
            $rules[] = PayslipHeader::validateRules($data);

            if ($formfillup_type === 'regular') {
                if( $group_dept !='' && $subject !=''){
                    $rules[] = [
                        'level' => 'required',
                        'session' => 'required',
                        ];
                }
                elseif  ($level == 0 || $session == 0 || $subject == 0) {
                    $rules[] = [
                        'level' => 'required',
                        'session' => 'required',
                        // 'subject' => $pro_group == 'Degree'? 'max:100' : 'required',
                        ];
                }
            }

            $this->validate($request, Arr::collapse($rules));

            $code = $exam_year.'_'.$type.'_'.$level.'_'.$pro_group.'_'.$group_dept.'_'.$subject;
            $payslip_header->title = $request->get('title');
            $payslip_header->start_date = $request->get('start_date');
            $payslip_header->end_date = $request->get('end_date');
            $payslip_header->pro_group = $pro_group;
            $payslip_header->group_dept = $group_dept;
            $payslip_header->subject = $subject;
            $payslip_header->type = $type;
            $payslip_header->level = $level;
            $payslip_header->code = $code;
            $payslip_header->session = $session;
            $payslip_header->exam_year = $exam_year;
            $payslip_header->student_type = $student_type;
            $payslip_header->formfillup_type = $formfillup_type;
            $payslip_header->total_papers = $total_papers;

            $payslip_header->save();

            $header_id = $payslip_header->id;
            
            $code = $header_id.'_'.$exam_year.'_'.$type.'_'.$level.'_'.$pro_group.'_'.$group_dept.'_'.$subject.'_'.$session.'_'.$formfillup_type;

            $payslip_header = PayslipHeader::find($header_id);
            $payslip_header->code = $code;
            $payslip_header->update();

            if($action_type =='duplicate'){
                $item_ids = [];
                $payslip_items = PayslipItem::where('payslipheader_id',$id)->get();
                foreach ($payslip_items as $item) {
                    $n_item = new PayslipItem;
                    $n_item->payslipheader_id = $header_id;
                    $n_item->item = $payslip_header->title;
                    $n_item->type_id = $item->type_id;
                    $n_item->save();
                    $item_ids[] = $n_item->id;
                }

                $generatorsGroup = PayslipGenerator::where('payslipheader_id', $id)->groupBy('paysliptitle_id')->get();

                if(count($generatorsGroup) > 0){
                    $title = new PayslipTitle;
                    $title->title = $payslip_header->title;
                    $title->payslipheader_id = $payslip_header->id;
                    $title->status = 1;
                    $title->save();

                    $items = PayslipItem::where('payslipheader_id', $id)->get();

                    foreach ($items as $key => $item) {
                        $generators = PayslipGenerator::where('payslipheader_id', $id)->where('payslipitem_id', $item->id)->get();
                        foreach ($generators as $generator) {
                            $gen = new PayslipGenerator;
                            $gen->paysliptitle_id = $title->id;
                            $gen->payslipheader_id = $payslip_header->id;
                            $gen->payslipitem_id = $item_ids[$key];
                            $gen->fees = $generator->fees;
                            $gen->save();
                        }
                    }

                }
            }

            DB::commit();

            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $payslip_header->id

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
        $header = PayslipHeader::find($id);

        $html =  view('BackEnd.admin.payslip_header.particles.show', compact('header'))->render();

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
    public function edit(Request $request, $id)
    {
        $header = PayslipHeader::find($id);
        $data = [
            'title' => $header->title,
            'subject' => explode('_',$header->subject),
            'total_papers' => $header->total_papers,
            'code' => $header->code,
            'start_date' => $header->start_date,
            'end_date' => $header->end_date,
            'header_type' => $header->type,
            'level' => $header->level,
            'pro_group' => $header->pro_group,
            'faculty' => explode('_',$header->group_dept),
            'session' => explode('_',$header->session),
            'exam_year' => $header->exam_year,
            'student_type' => $header->student_type,
            'formfillup_type' => explode('_',$header->formfillup_type)
        ];

        $html = view('BackEnd.admin.payslip_header.particles.form', $data)->render();
        return response()->json([
                'status' => 200,
                'modal' => 'modal-lg',
                'html' => $html

            ],Response::HTTP_OK);
    }

    public function fields(Request $request, $id){
        $header = PayslipHeader::find($id);

        $html =  view('BackEnd.admin.payslip_header.particles.field', compact('header'))->render();

        return response()->json([
                'status' => 202,
                'modal' => 'modal-lg',
                'html' => $html
        ],Response::HTTP_OK);
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
            $payslip_header = PayslipHeader::find($id);

            $payslip_items = PayslipItem::where('payslipheader_id',$id)->get();
            foreach ($payslip_items as $item) {
                PayslipItem::find($item->id)->delete();
            }

            $generators = PayslipGenerator::where('payslipheader_id', $id)->get();
            foreach($generators as $generator){
                $titles = PayslipTitle::where('id',$generator->paysliptitle_id)->get();
                if(count($titles) > 0)
                    PayslipTitle::find($generator->paysliptitle_id)->delete();
            }

            $generators = PayslipGenerator::where('payslipheader_id', $id)->get();
            foreach($generators as $generator){
                PayslipGenerator::find($generator->id)->delete();
            }

            $payslip_header->delete();
        return response()->json([
                'status' => 'warning',
                'message' => 'Payslip Header Deleted Successfully',
                'id' => $id,
                'table' => 'datatable'

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }
}
