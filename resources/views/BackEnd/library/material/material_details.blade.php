<script>  function PrintElem(elem)
    {
     
        Popup($(elem).html());
    }

    function Popup(data) 
    {
        var mywindow = window.open('', 'print_details', 'height=562,width=795');
        mywindow.document.write('<html><head><title>print_details</title>');
    //mywindow.document.write('<link rel="stylesheet" type="text/css" href="main.css"/>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.document.close();
        mywindow.print();
        return true;
    }

</script>

<style type="text/css">
    table.pagination2 {
    font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
    font-size: 12px;
    width: 100%;
    height: 100%;
    text-align: left;
    border: 1px solid #ccc;
    margin-top: 10px;
}
</style>
<?php 
 
 $keys=array("Physical Form",'Accession No','Call No','ISBN','Date','Subject','Title','Subtitile','Author','Editor','Location','Size','No of Pages','Edition','Edition Year','Publisher','Publishing Year','Publication Place','Clue Page', 'Price','Dues','Source Details','Series','Note','Available','Condition');  
 $values=array('physical_form','id','call_no','isbn','add_date','subject','title','subtitle','author','editor','location','size','no_of_page','edition','edition_year','publisher','publishing_year','place_of_publication','clue_page','price','due','source_details','series','note','available','book_condition');
 ?>
   <!--print button-->
  <center><input type="button" value="Print Details" onClick="PrintElem('#print_details')" /></center>
  
  <div id="print_details">
  <table border='0' class='pagination2' align='center' width='100%'>

    @php
        $num_field = count($values);
    @endphp

    @for($i= 0; $i< $num_field; $i++)
        <tr>
            <td style='padding:4px 0 4px 40px; width:40%'>{{$keys[$i]}}</td>
            @php $value = $values[$i] @endphp

            @if($value == 'subject')
                <td style='padding:2px 0 2px 10px; width:60%'>
                <?php
        
                  $msubjects = App\Models\Msubject::whereMaterial_id($material->id)->get();
                  $subject_count = App\Models\Msubject::whereMaterial_id($material->id)->count();
                  $j=1; 
        
                ?>

                @foreach($msubjects as $msubject)
                  @if($j == $subject_count)
                    {{ ucfirst($msubject->subject->name) }}
                  @else
                    {{ ucfirst($msubject->subject->name) . ',' }}
                  @endif
    
                  <?php $j++; ?>
                @endforeach
                </td>
            @else
                <td style='padding:2px 0 2px 10px; width:60%'>{{$material->$value}}</td>
            @endif
        </tr>
    @endfor
    <tr>
        <td align='center' bgcolor='#fff'>Accession No<br/><img src="{{url('/barcode/')."/barcode.php?code={$material->maccessions[0]->accession_no}"}}" alt="Your generated image" /></td>
        <td align='center' bgcolor='#fff'>Library Call No<br/><img src="{{url('/barcode/')."/barcode.php?code={$material->call_no}"}}" alt="Your generated image" /></td>
    </tr>
  </table>
 
 </div>
 