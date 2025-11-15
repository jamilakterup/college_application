<?php

use App\Libs\Study;
use App\Models\Department;
use App\Models\IdRoll;

/**
 * @author Rajib, cse,Ru.
 *
 * @category file
 * @copyright 2016
 */

?>


<?php


class IdRollGenerate
{


  function __construct() {}




  public static function hons_id_generate($session, $subject, $prefix)
  {
    $id_table_subject = $prefix . $subject;

    // Get last digit used
    $results = DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$id_table_subject'");
    $digit = 1; // Default
    foreach ($results as $result) {
      $digit = $result->last_digit_used + 1;
      break;
    }
    // Format digit: no leading zeros for numbers >= 100, otherwise pad to 2 digits
    $formatted_digit = $digit >= 100 ? (string)$digit : str_pad($digit, 2, '0', STR_PAD_LEFT);

    // Get department code
    $results = DB::select("select dept_code from departments where dept_name='$subject'");
    $dept_code = '';
    foreach ($results as $result) {
      $dept_code = $result->dept_code;
      break;
    }

    $session_parts = explode('-', $session);
    $start_year = substr($session_parts[0], 2, 2);
    $end_year = substr($session_parts[1], 2, 2);

    $class_roll = $start_year . $end_year . $dept_code . $formatted_digit;

    return $class_roll;
  }

  public static function hons_roll_generate($id)
  {

    return $id;
  }


  public static function Honours_id_generate($session, $class_roll, $catagory)
  {

    $session = substr($session, 2, 2);  // take session as first year of the session(ex: 2012-2013 , session is 2012)
    return $id = $session . $class_roll;
  }

  public static function roll_generate_msc($session, $subject, $prefix)
  {
    $id_table_subject = $prefix . $subject;

    // Get last digit used
    $results = DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$id_table_subject'");
    $digit = 1; // Default
    foreach ($results as $result) {
      $digit = $result->last_digit_used + 1;
      break;
    }
    // Format digit: no leading zeros for numbers >= 100, otherwise pad to 2 digits
    $formatted_digit = $digit >= 100 ? (string)$digit : str_pad($digit, 2, '0', STR_PAD_LEFT);

    // Get department code
    $results = DB::select("select dept_code from departments where dept_name='$subject'");
    $dept_code = '';
    foreach ($results as $result) {
      $dept_code = $result->dept_code;
      break;
    }

    $session_parts = explode('-', $session);
    $start_year = substr($session_parts[0], 2, 2);
    $end_year = substr($session_parts[1], 2, 2);

    $class_roll = $start_year . $end_year . $dept_code . $formatted_digit;

    return $class_roll;
  }

  public static function id_generate_deg($session, $subject, $prefix)
  {
    if ($subject == 'B.A')  // in id_roll table, subject of hsc and degree are separated by prefix hsc_ and degree_ in 'dept_name' code.
      $cat = "1";
    elseif ($subject == 'B.S.S')
      $cat = "2";
    elseif ($subject == 'B.B.S')
      $cat = "3";
    elseif ($subject == 'B.Sc')
      $cat = "4";
    $subject = 'degree_' . $subject;

    $results = DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$subject'");

    //convert 1 as 001 for 4 digit roll
    foreach ($results as $result) {
      $digit = str_pad($result->last_digit_used + 1, '3', '0', STR_PAD_LEFT);
      break;
    }
    $session = substr($session, 2, 2);
    $id = $session . $cat . $digit;
    return $id;
  }

  public static function roll_generate_deg($id)
  {
    return $class_roll = substr($id, 4);
    // return $id;
  }

  // public static function id_generate_hsc($session,$groups){
  //     if($groups=='Humanities')  // in id_roll table, groups of hsc and degree are separated by prefix hsc_ and degree_ in 'dept_name' code.
  //     $cat="2";
  //     else if($groups=='Science')
  //       $cat="1";
  //     else if($groups=='Business Studies')
  //       $cat="3";
  //     // $cat = '000';
  //     $groups='hsc_'.$groups;

  //     $results= DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$groups'");
  //     //convert 1 as 001 for 3 digit roll
  //     foreach($results as $result){ $digit=str_pad($result->last_digit_used+1,'3','0',STR_PAD_LEFT); break; }
  //     $session=substr($session,0,4);
  //     $id=$session.$cat.$digit;

  //     return $id;
  // }

  // public static function roll_generate_hsc($id){
  //   return $id;
  // }

  public static function id_generate_hsc($session, $groups)
  {

    do {
      if ($groups == 'Humanities') $rand_number = random_int(1, 300);
      if ($groups == 'Science') $rand_number = random_int(1, 600);
      if ($groups == 'Business Studies') $rand_number = random_int(1, 300);

      $digit = str_pad($rand_number, '3', '0', STR_PAD_LEFT);
    } while (
      !empty(DB::table("student_info_hsc")->where('groups', $groups)->where('session', $session)->select(DB::raw("RIGHT(id, 3) as digit"))->having('digit', $digit)->groupBy('digit')->first())
    );

    if ($groups == 'Humanities')
      $cat = "2";
    else if ($groups == 'Science')
      $cat = "1";
    else if ($groups == 'Business Studies')
      $cat = "3";

    $session = substr($session, 0, 4);

    $id = $session . $cat . $digit;

    return $id;
  }

  public static function roll_generate_hsc($id)
  {
    return $id;
  }

  public static function id_generate_hsc_store($session, $groups)
  {
    if ($groups == 'Humanities')
      $cat = "2";
    else if ($groups == 'Science')
      $cat = "1";
    else if ($groups == 'Business Studies')
      $cat = "3";
    $groups = 'hsc_' . $groups;

    $results = DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$groups'");
    //convert 1 as 001 for 3 digit roll
    foreach ($results as $result) {
      $digit = str_pad($result->last_digit_used + 1, '3', '0', STR_PAD_LEFT);
      break;
    }

    $session = substr($session, 0, 4);
    $id = $session . $cat . $digit;

    return $id;
  }

  public static function roll_generate_hsc_store($id)
  {
    return $id;
  }

  public static function id_generate_msc1st($session, $subject, $prefix)
  {
    $id_table_subject = $prefix . $subject;

    $results = DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$id_table_subject'");
    //convert 1 as 001 for 3 digit roll
    foreach ($results as $result) {
      $digit = str_pad($result->last_digit_used + 1, '3', '0', STR_PAD_LEFT);
      break;
    }

    $results = DB::select("select dept_code from departments where dept_name='$subject'");
    foreach ($results as $result) {
      $dept_code = $result->dept_code;
      break;
    }

    // $session=substr($session,2,2);
    $session = substr($session, 2, 2);
    //$dept_code=substr($dept_code,0,2); // take first two digit of the department code

    $class_roll = $session . '1' . $dept_code . $digit;


    return $class_roll;
  }

  public static function roll_generate_msc1st($id)
  {
    return $id;
  }
}


?>