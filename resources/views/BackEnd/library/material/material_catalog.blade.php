<?php 
		foreach($materials as $result)
		{   		   
		   $id=$result->maccessions[0]->accession_no;
		   $xisbn=$result->isbn;
		   $xcall_no=$result->call_no;
		   $xadd_date=$result->add_date;
		   $xbook_type=$result->type;
		   $xbook_title=$result->title;
		   $xbook_subtitle=$result->subtitle;
		   $xauthor=$result->author;
		   $xbook_size=$result->size;
		   $xpublisher=$result->publisher;
		   $xpublishing_year=$result->publishing_year;
		   $xpublication_place=$result->place_of_publication;
		   $xedition=$result->edition;
		   $xedition_year=$result->edition_year;
		   $xprice=$result->price;
		   $xdue=$result->due;
		   $xlocation=$result->location;
		   $xseries=$result->series;
		   $xsize=$result->size;
		   $xnote=$result->note;
		   $xeditor=$result->editor;
		}
		if($xisbn!='')
			$query=DB::select("select * from book_info where isbn='$xisbn' and book_condition='usable' and call_no='$xcall_no';");
		else
			$query=DB::select("select * from book_info where title='$xbook_title' and author='$xauthor' and book_condition='usable' and call_no='$xcall_no';");
					
		$results = $query;
		$no_book=0;
		foreach($results as $result)
		{   		   
		  $no_book++;
		} 
		
		$xbook_type=str_replace(',',' / ',$xbook_type);
		
	    $xauthor_array=explode(',',$xauthor);
		$temp1=$xauthor_array[0];
		$temp2=explode(' ',$temp1);
		$count=count($temp2);
		$xauthor_last=$temp2[$count-1];
		$xauthor_first='';
		for($i=0;$i<$count-1;$i++)
		$xauthor_first=$xauthor_first.' '.$temp2[$i];
		
?>
<script>  function PrintElem(elem)
    {
     
        Popup($(elem).html());
    }

    function Popup(data) 
    {
        var mywindow = window.open('', 'print_slip', 'height=562,width=795');
        mywindow.document.write('<html><head><title>print_slip</title>');
    //mywindow.document.write('<link rel="stylesheet" type="text/css" href="main.css"/>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.document.close();
        mywindow.print();
        return true;
    }

</script>
<div id='print_slip'>

<table border="1"  class="catalog"  cellspacing="2px" align="center" height="200" width="400">
<caption>Books Catalog</h3></caption>
<tr>
	<td><?php echo $xcall_no; ?></td>
	<td><?php echo "";?></td>
</tr>
<tr height="240px" width="100%" valign="top">
	<td><?php echo "Copy-".$no_book; ?></td>
	
	<td class="important">
		<?php echo $xauthor_last.','.$xauthor_first; ?><br/>
		<span class="title">
		 <?php
			 echo $xbook_title; 
			 if($xbook_subtitle!='') echo " : ".$xbook_subtitle;?>
		 </span>
		 <?php echo " / ".$xauthor;?>
		 <?php if($xeditor!='')echo " (".$xeditor.") ";?>
		 <?php 
		 	if($xedition!='')
			echo " - ".$xedition." ed. ";
		 ?>
		 
		 <?php
		 	if($xedition_year!=0)
			echo $xedition_year;
			
			
		 ?>
		 
		 <?php
		 
		 	if($xpublisher!='' || $xpublication_place!='' || $xpublishing_year!='')
			echo "<br/> ";
			
		 	if($xpublisher!='')
			echo $xpublisher;
		 ?>
		 
		 <?php
		 	if($xpublication_place!='')
			echo ", ".$xpublication_place;
		 ?>
		 <?php  
		 	if($xpublishing_year!=0)
			echo ", ".$xpublishing_year." (".$xseries.") ";
		 ?>
		 <?php 
		 	if($xsize!='' || $xisbn!='' || $xnote!='')
			echo "<br/><br/>";
			if($xsize!='')
		 	echo $xsize;
		 	if($xisbn!='')
			echo ", ".$xisbn;
		 	if($xnote!='')
			echo ", ".$xnote;?><br/>
		 <p align="center">Accession No:<?php echo"<br/><img  src=\"".url('/')."/barcode/barcode.php?code={$id}"."\" alt=\"Your generated image\" /> <br/> </p>"; 
		?>
	</td>
	
</tr>

</table>

</div>
<center><br/><input type="button" value="Print Catalog" onClick="PrintElem('#print_slip')" /></center>