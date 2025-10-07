<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{{$current_level}} Form Fillup Student Lists, Exam Year-{{$exam_year}}</title>
</head>
<body>
<style>
.page-break {
    page-break-after: always;
}
html, body, div,fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td,{margin: 0; padding: 0; border: 0; outline: 0; font-weight: inherit; font-style: inherit; font-size: 100%; font-family: inherit; vertical-align:top;}:focus {outline: 0;}

table {border-collapse: collapse; border-spacing: 0;} input, select {vertical-align:middle;} abbr[title], dfn[title] {border-bottom:1px dotted; cursor:help;} 
body {font-family: 'FreeSerif',sans-serif;}
td.title{ font-size:12px; line-height:36px; color:#000;}
td.subtitle{ font-size:12px; line-height:30px; color:#000;}
td.mintitle{ font-size:12px; line-height:24px; color:#000;}
.order-details tr, .order-details tr td{ border:1px solid #cecece; padding:5px 10px;}

</style>

<h2 style="text-align: center;">{{$current_level}} Form Fillup Student Lists, Exam Year-{{$exam_year}}</h2>

<table class="order-details" border="1" width="100%" style="margin-top:10px;">
	
	<tr>
	    <th style="border:1px solid #cecece;">Si No</th>
		<th style="border:1px solid #cecece;">Registration ID</th>
		<th style="border:1px solid #cecece;">Form Fillup Level</th>
		<th style="border:1px solid #cecece;">Groups</th>
		<th style="border:1px solid #cecece;">Paid Amount</th>
		<th style="border:1px solid #cecece;">Transaction ID</th>
		<th style="border:1px solid #cecece;">Registration Type</th>
		<th style="border:1px solid #cecece;">Exam Year</th>
		@if($from_date)
			<th style="border:1px solid #cecece;">From Date</th>
			<th style="border:1px solid #cecece;">To Date</th>
		@endif
	</tr>
<?php $si=0; ?>
	<?php foreach($records as $college) {?>
				<?php $si++; ?>
				<tr class="">
				    <td style="font-size:12px;"><?php echo $si ?></td>
					<td style="font-size:12px;"><?php echo $college->id ?></td>
					<td style="font-size:10px;"><?php echo $college->level_study ?></td>
					<td style="font-size:12px;"><?php echo $college->groups ?></td>			
					<td style="font-size:12px;"><?php echo $college->total_amount ?></td>		
					<td style="font-size:12px;"><?php echo $college->transaction_id ?></td>		
					<td style="font-size:12px;"><?php echo $college->formfillup_type ?></td>		
					<td style="font-size:12px;"><?php echo $college->exam_year ?></td>
					@if($from_date)
						<td style="font-size:12px;">{{$from_date}}</td>
						<td style="font-size:12px;">{{$to_date}}</td>
					@endif
									
					
				</tr>	

			<?php } ?>

</table>
   
</body>
</html>