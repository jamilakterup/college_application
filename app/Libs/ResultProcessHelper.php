<?php

namespace App\Libs;

use App\Models\ClasseTestExam;
use App\Models\Exam;
use App\Models\HscGpa;
use App\Models\Mark;
use App\Models\SubjectParticle;
use App\Models\StudentSubMarkGp;
use App\Models\ConfigExamParticle;
use App\Models\GradeScale;
use App\Models\GradeSystem;
use Ecm;

class ResultProcessHelper{
	public static function processSubjectMarks($subject_id, $student_info, $session, $group_id, $exam_id, $curr_level, $exam_year, $fourth) {
	    $marks = Mark::where('student_id', $student_info->id)
	        ->where('session', $session)
	        ->where('group_id', $group_id)
	        ->where('exam_id', $exam_id)
	        ->where('exam_year', $exam_year)
	        ->where('subject_id', $subject_id)
	        ->get();

	    if ($marks->count() > 0) {
	        $total_obtained = 0;
	        $subject_total = 0;
	        $isAbsent = false;
	        $hasFailed = false;

	        $subject_particles = SubjectParticle::where('classe_id', $curr_level->id)
	            ->where('group_id', $group_id)
	            ->where('subject_id', $subject_id)
	            ->get();

	        // Calculate total and converted marks
	        $sub_total_convert = $subject_particles->sum('total_converted') ?: 100;

	        foreach ($marks as $mark) {
	            if (is_numeric($mark->converted_mark)) {
	                $exam_particle = ConfigExamParticle::where('classe_id', $curr_level->id)
	                    ->where('group_id', $group_id)
	                    ->where('subject_id', $subject_id)
	                    ->where('xmparticle_id', $mark->particle_id)
	                    ->first();

	                $pass_particle_convert = $exam_particle->pass_particle_convert ?? 0;
	                $particle_convert = $exam_particle->particle_convert ?? 0;

	                if ($mark->converted_mark < $pass_particle_convert) {
	                    $hasFailed = true;
	                }

	                $total_obtained += $mark->converted_mark;
	                $subject_total += $particle_convert;
	            } else {
	                $isAbsent = true;
	            }
	        }

	        // Calculate final subject mark
	        $subject_mark = ($subject_total != 0) 
	            ? ceil(round((($total_obtained / $subject_total) * $sub_total_convert), 2)) 
	            : 0;

	        $grade = 'F';
	        $point = 0;

	        $absent = $isAbsent ? 1 : 0;
	        if (!$isAbsent && !$hasFailed) {
	            $g_and_p = self::gradePoint($total_obtained, $subject_total);
	            $grade = $g_and_p['grade'];
	            $point = $g_and_p['point'];
	        }

	        // Insert data into StudentSubMarkGp
	        StudentSubMarkGp::create([
	            'student_id' => $student_info->id,
	            'session' => $session,
	            'group_id' => $group_id,
	            'exam_year' => $exam_year,
	            'exam_id' => $exam_id,
	            'subject_id' => $subject_id,
	            'total_mark' => $subject_mark,
	            'grade' => $grade,
	            'point' => $point,
	            'fourth' => $fourth,
	            'absent' => $absent,
	        ]);
	    }
	}


	public static function processSubject($student_sub, $subject_id, $student_info, $session, $group_id, $exam_id, $curr_level, $fourthFlag)
	{
		if ($subject_id != 0) {
			$total_obt_sub = 0;
			$subject_total = 0;
			$ab_sub = 0;
			$sub_fail = 0;

			$subject_particals = SubjectParticle::where('classe_id', $curr_level)->where('group_id', $group_id)->where('subject_id', $subject_id)->get();
			$sub_total_convert = 100;

			foreach ($subject_particals as $subject_partical) {
				$sub_total_mark = $subject_partical->total;
				$sub_total_convert = $subject_partical->total_converted;
			}

			$marks = Mark::whereStudent_id($student_info->id)
			->whereSession($session)
			->whereGroup_id($group_id)
			->whereExam_id($exam_id)
			->whereSubject_id($subject_id)
			->whereClass_test_id($class_test->class_test_id)
			->get();

			foreach ($marks as $mark) {
				if (is_numeric($mark->mark)) {
					$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
					->whereGroup_id($group_id)
					->whereSubject_id($subject_id)
					->whereXmparticle_id($mark->particle_id)
					->first();

					if ($mark->converted_mark < $exam_particle->pass_particle_convert) {
						$sub_fail = 1;
					}

					$total_obt_sub += $mark->converted_mark;
					$subject_total += $exam_particle->particle_convert;
				} else {
					$ab_sub = 1;
				}
			}

			$subject_mark = ($subject_total != 0) 
		    ? ceil((($total_obt_sub / $subject_total) * $sub_total_convert))
		    : 0;



			if ($ab_sub == 0) {
				if ($sub_fail != 1) {
					$g_and_p = self::gradePoint($total_obt_sub, $subject_total);
					$subject_particle = SubjectParticle::whereClasse_id($curr_level->id)
					->whereGroup_id($group_id)
					->whereSubject_id($subject_id)
					->first();

					$total_obt_sub = ($subject_particle->total != 0) 
					? ($total_obt_sub * $subject_particle->total_converted) / $subject_particle->total 
					: 0;

	                // Insert grade point
					$insert = new StudentSubMarkGp;
					$insert->student_id = $student_info->id;
					$insert->session = $session;
					$insert->group_id = $group_id;
					$insert->exam_id = $exam_id;
					$insert->subject_id = $subject_id;
					$insert->total_mark = $subject_mark;
					$insert->grade = $g_and_p['grade'];
					$insert->point = $g_and_p['point'];
	                $insert->fourth = $fourthFlag; // set fourth flag
	                $insert->save();
	            } else {
	                // Failed case
	            	$insert = new StudentSubMarkGp;
	            	$insert->student_id = $student_info->id;
	            	$insert->session = $session;
	            	$insert->group_id = $group_id;
	            	$insert->exam_id = $exam_id;
	            	$insert->subject_id = $subject_id;
	            	$insert->total_mark = $subject_mark;
	            	$insert->grade = 'F';
	            	$insert->point = 0;
	                $insert->fourth = $fourthFlag; // set fourth flag
	                $insert->save();
	            }
	        } else {
	            // Absent case
	        	$insert = new StudentSubMarkGp;
	        	$insert->student_id = $student_info->id;
	        	$insert->session = $session;
	        	$insert->group_id = $group_id;
	        	$insert->exam_id = $exam_id;
	        	$insert->subject_id = $subject_id;
	        	$insert->total_mark = $subject_mark;
	        	$insert->grade = 'F';
	        	$insert->point = 0;
	            $insert->fourth = $fourthFlag; // set fourth flag
	            $insert->absent = 1;
	            $insert->save();
	        }
	    }
	}

	public static function calculateCGPA($all_sub_marks) {
	    $cgpa = $fail = $without_4th = $grand_cgpa = $forth_count = 0;

	    foreach ($all_sub_marks as $all) {
	        $point = is_numeric($all->point) ? $all->point : 0;

	        if ($all->fourth != 1) {
	            $cgpa += $point;
	            $without_4th += $point;
	            if ($point == 0) $fail = 1;
	        } else {
	            $forth_count++;
	            if ($point > 2) $cgpa += $point - 2;
	        }
	    }

	    return [$cgpa, $fail, $without_4th, $forth_count];
	}


	/// process test

	public static function processTestSubjectMarks($subject_id, $student_info, $session, $group_id, $exam_id, $curr_level) {
	    $total_obtained = 0;
	    $subject_total = 0;
	    $isAbsent = false;
	    $hasFailed = false;

	    $subject_particles = SubjectParticle::where('classe_id', $curr_level->id)
	                        ->where('group_id', $group_id)
	                        ->where('subject_id', $subject_id)
	                        ->get();
	                        
	    // Calculate total and converted marks
	    $sub_total_convert = $subject_particles->sum('total_converted') ?: 100;
	    
	    $class_tests = ClasseTestExam::where('exam_id', $exam_id)->get();
	    foreach ($class_tests as $test) {
	        $marks = ClassTestMark::where('student_id', $student_info->id)
	                 ->where('session', $session)
	                 ->where('group_id', $group_id)
	                 ->where('exam_id', $exam_id)
	                 ->where('subject_id', $subject_id)
	                 ->where('class_test_id', $test->class_test_id)
	                 ->get();

	        foreach ($marks as $mark) {
	            if (is_numeric($mark->mark)) {
	                $exam_particle = ConfigExamParticle::where('classe_id', $curr_level->id)
	                                ->where('group_id', $group_id)
	                                ->where('subject_id', $subject_id)
	                                ->where('xmparticle_id', $mark->particle_id)
	                                ->first();
	                                
	                if ($mark->converted_mark < ($exam_particle->pass_particle_convert ?? 0)) {
	                    $hasFailed = true;
	                }
	                $total_obtained += $mark->converted_mark;
	                $subject_total += $exam_particle->particle_convert ?? 0;
	            } else {
	                $isAbsent = true;
	            }
	        }
	    }
	    
	    // Calculate final subject mark
	    $subject_mark = ($subject_total != 0) 
	    ? ceil((($total_obtained / $subject_total) * $sub_total_convert))
	    : 0;

	    // Insert data into StudentSubMarkGp
	    $grade = 'F';
	    $point = 0;
	    $fourth = 0;
	    $absent = $isAbsent ? 1 : 0;

	    if (!$isAbsent) {
	        if (!$hasFailed) {
	            $g_and_p = self::gradePoint($total_obtained, $subject_total);
	            $grade = $g_and_p['grade'];
	            $point = $g_and_p['point'];
	        }
	    }

	    StudentSubMarkGp::create([
	        'student_id' => $student_info->id,
	        'session' => $session,
	        'group_id' => $group_id,
	        'exam_id' => $exam_id,
	        'subject_id' => $subject_id,
	        'total_mark' => $subject_mark,
	        'grade' => $grade,
	        'point' => $point,
	        'fourth' => $fourth,
	        'absent' => $absent,
	    ]);
	}

	public static function processTestSubject($student_sub, $subject_id, $student_info, $session, $group_id, $exam_id, $curr_level, $fourthFlag)
	{
	    if ($subject_id != 0) {
	        $total_obt_sub = 0;
	        $subject_total = 0;
	        $ab_sub = 0;
	        $sub_fail = 0;

	        $subject_particals = SubjectParticle::where('classe_id', $curr_level)->where('group_id', $group_id)->where('subject_id', $subject_id)->get();
	        $sub_total_convert = 100;

	        foreach ($subject_particals as $subject_partical) {
	            $sub_total_mark = $subject_partical->total;
	            $sub_total_convert = $subject_partical->total_converted;
	        }

	        $class_tests = ClasseTestExam::where('exam_id', $exam_id)->get();
	        foreach ($class_tests as $class_test) {
	            $marks = ClassTestMark::whereStudent_id($student_info->id)
	                ->whereSession($session)
	                ->whereGroup_id($group_id)
	                ->whereExam_id($exam_id)
	                ->whereSubject_id($subject_id)
	                ->whereClass_test_id($class_test->class_test_id)
	                ->get();

	            foreach ($marks as $mark) {
	                if (is_numeric($mark->mark)) {
	                    $exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
	                        ->whereGroup_id($group_id)
	                        ->whereSubject_id($subject_id)
	                        ->whereXmparticle_id($mark->particle_id)
	                        ->first();

	                    if ($mark->converted_mark < $exam_particle->pass_particle_convert) {
	                        $sub_fail = 1;
	                    }

	                    $total_obt_sub += $mark->converted_mark;
	                    $subject_total += $exam_particle->particle_convert;
	                } else {
	                    $ab_sub = 1;
	                }
	            }
	        }

	        $subject_mark = ($subject_total != 0) 
		    ? ceil((($total_obt_sub / $subject_total) * $sub_total_convert))
		    : 0;


	        if ($ab_sub == 0) {
	            if ($sub_fail != 1) {
	                $g_and_p = self::gradePoint($total_obt_sub, $subject_total);
	                $subject_particle = SubjectParticle::whereClasse_id($curr_level->id)
	                    ->whereGroup_id($group_id)
	                    ->whereSubject_id($subject_id)
	                    ->first();

	                $total_obt_sub = ($subject_particle->total != 0) 
	                    ? ($total_obt_sub * $subject_particle->total_converted) / $subject_particle->total 
	                    : 0;

	                // Insert grade point
	                $insert = new StudentSubMarkGp;
	                $insert->student_id = $student_info->id;
	                $insert->session = $session;
	                $insert->group_id = $group_id;
	                $insert->exam_id = $exam_id;
	                $insert->subject_id = $subject_id;
	                $insert->total_mark = $subject_mark;
	                $insert->grade = $g_and_p['grade'];
	                $insert->point = $g_and_p['point'];
	                $insert->fourth = $fourthFlag; // set fourth flag
	                $insert->save();
	            } else {
	                // Failed case
	                $insert = new StudentSubMarkGp;
	                $insert->student_id = $student_info->id;
	                $insert->session = $session;
	                $insert->group_id = $group_id;
	                $insert->exam_id = $exam_id;
	                $insert->subject_id = $subject_id;
	                $insert->total_mark = $subject_mark;
	                $insert->grade = 'F';
	                $insert->point = 0;
	                $insert->fourth = $fourthFlag; // set fourth flag
	                $insert->save();
	            }
	        } else {
	            // Absent case
	            $insert = new StudentSubMarkGp;
	            $insert->student_id = $student_info->id;
	            $insert->session = $session;
	            $insert->group_id = $group_id;
	            $insert->exam_id = $exam_id;
	            $insert->subject_id = $subject_id;
	            $insert->total_mark = $subject_mark;
	            $insert->grade = 'F';
	            $insert->point = 0;
	            $insert->fourth = $fourthFlag; // set fourth flag
	            $insert->absent = 1;
	            $insert->save();
	        }
	    }
	}

	public static function saveGPA($student_id, $session, $group_id, $exam_id, $exam_year, $all_sub_marks, $gpaData) {
	    list($cgpa, $fail, $without_4th, $forth_count) = $gpaData;
	    $no_sub = count($all_sub_marks) - $forth_count;

	    if ($fail != 1) {
	        if ($no_sub > 0) {
	            $grand_cgpa = $cgpa > ($no_sub * 5) ? 5 : $cgpa / $no_sub;
	            $without_4th_grand_cgpa = $without_4th / $no_sub;
	            $grade = Ecm::grade($grand_cgpa);
	        } else {
	            $grand_cgpa = $without_4th_grand_cgpa = 0;
	            $grade = 'F';
	        }
	    } else {
	        $grand_cgpa = $without_4th_grand_cgpa = 0;
	        $grade = 'F';
	    }

	    // Insert GPA
	    $insert_gpa = new HscGpa;
	    $insert_gpa->student_id = $student_id;
	    $insert_gpa->session = $session;
	    $insert_gpa->group_id = $group_id;
	    $insert_gpa->exam_id = $exam_id;
	    $insert_gpa->exam_year = $exam_year;
	    $insert_gpa->cgpa = $grand_cgpa;
	    $insert_gpa->without_4th = $without_4th_grand_cgpa;                    
	    $insert_gpa->grade = $grade;
	    $insert_gpa->save();
	}

	public static function gradePoint($mark, $total)
	{
	    $percentage_mark = ceil(($mark * 100) / $total);

	    $active_grade_system = GradeSystem::whereStatus(1)->first();
	    $grade_scales = GradeScale::whereGradesystem_id($active_grade_system->id)->get();

	    $grade = 'F';
	    $point = 0;

	    foreach ($grade_scales as $grade_scale) {
	        $range_low = $grade_scale->range_low;
	        $range_high = $grade_scale->range_high;

	        if ($percentage_mark >= $range_low && $percentage_mark <= $range_high) {
	            $grade = $grade_scale->letter_grade;
	            $point = $grade_scale->point;
	            break;
	        }
	    }

	    $result = [
	        'grade' => $grade,
	        'point' => $point,
	    ];

	    return $result;
	}

}