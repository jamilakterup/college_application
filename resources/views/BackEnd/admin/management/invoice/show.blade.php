<style type="text/css">
	table tr td:nth-child(1) {
    font-weight: bold;
    font-size: 1rem;
  }
</style>

<div class="col-lg-12 col-md-12 col-sm-12">
    <div class="table-responsive table-bordered">
        <table class="table">
            <tbody>
                <tr>
                    <td>Name</td>
                    <td>{{$invoice->name}}</td>
                </tr>
                <tr>
                    <td>Roll</td>
                    <td>{{$invoice->roll}}</td>
                </tr>

                <tr>
                    <td>Invoice Type</td>
                    <td>{{$invoice->type}}</td>
                </tr>
                <tr>
                    <td>Level</td>
                    <td>{{$invoice->level}}</td>
                </tr>
                <tr>
                    <td>Pro Group</td>
                    <td>{{$invoice->pro_group}}</td>
                </tr>
                <tr>
                    <td>Subject</td>
                    <td>{{$invoice->subject}}</td>
                </tr>
                <tr>
                    <td>Session</td>
                    <td>{{$invoice->admission_session}}</td>
                </tr>
                <tr>
                    <td>Exam Year</td>
                    <td>{{$invoice->passing_year}}</td>
                </tr>
                <tr>
                    <td>Start Date</td>
                    <td>{{$invoice->date_start}}</td>
                </tr>
                <tr>
                    <td>End Date</td>
                    <td>{{$invoice->date_end}}</td>
                </tr>
                <tr>
                    <td>Slip Name</td>
                    <td>{{$invoice->slip_name}}</td>
                </tr>
                <tr>
                    <td>Slip Type</td>
                    <td>{{$invoice->slip_type}}</td>
                </tr>
                <tr>
                    <td>Registration Type</td>
                    <td>{{$invoice->registration_type}}</td>
                </tr>
                <tr>
                    <td>Total Paper</td>
                    <td>{{$invoice->total_papers}}</td>
                </tr>

                <tr>
                    <td>Total Amount</td>
                    <td class="text-warning" style="font-weight: bold; font-size: 1.2rem;">{{$invoice->total_amount}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>