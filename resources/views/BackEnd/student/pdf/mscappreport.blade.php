<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Masters Application Student Lists, Session-{{$session}}</title>
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

<h2 style="text-align: center;">Honours Application Student Lists {{$session !='' ? 'Session: '.$session:''}}</h2>

<table class="order-details" border="1" width="100%" style="margin-top:10px;">
	
	<tr>
	    <th style="border:1px solid #cecece;">Si No</th>
		<th style="border:1px solid #cecece;">Admission Roll</th>
		<th style="border:1px solid #cecece;">Name</th>
		<th style="border:1px solid #cecece;">Contact No</th>
		<th style="border:1px solid #cecece;">Paid Amount</th>
		<th style="border:1px solid #cecece;">Paid Date</th>
	</tr>
<?php $si=0; ?>
	<?php foreach($applications as $application) {?>
				<?php $si++; ?>
				<tr class="">
				    <td style="font-size:12px;"><?php echo $si ?></td>
					<td style="font-size:12px;"><?php echo $application->admission_roll ?></td>
					<td style="font-size:12px;"><?php echo $application->name ?></td>	
					<td style="font-size:12px;"><?php echo $application->contact_no ?></td>
					<td style="font-size:12px;"><?php echo $application->total_amount ?></td>		
					<td style="font-size:12px;"><?php echo $application->date ?></td>		
				</tr>	

			<?php } ?>

</table>
   
</body>
</html>