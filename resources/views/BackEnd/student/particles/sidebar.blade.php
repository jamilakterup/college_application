<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-category">Students</li>
                    @canany([
                        'student.admission.manage',
                        'hsc.admission.index',
                        'hsc.admission.upload_student',
                        'honours.admission.index',
                        'student.admission.manage',
                        'degree.admission.index',
                        'degree.admission.upload_student',
                        'masters.admission.index',
                        'masters.admission.upload_student',
                        'hsc.admission.meritlist',
                        'hsc.admission.totlist',
                        'hsc.admission.registered_student',
                        'honours.admission.upload_student',
                        'honours.admission.meritlist',
                        'honours.admission.registered_student',
                        'degree.admission.meritlist',
                        'degree.admission.registered_student',
                        'masters.admission.meritlist',
                        'masters.admission.registered_student
                        ',
                        ])
                        <li class="site-menu-item has-sub">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                                <span class="site-menu-title">Admission</span>
                                <span class="site-menu-arrow"></span>
                            </a>

                            <ul class="site-menu-sub" style="">
                                <li class="site-menu-item {{ active('students/hsc*') }}">
                                    <a href="{{ route('student.hsc') }}">
                                        <span class="site-menu-title">HSC</span>
                                    </a>
                                </li>
                            </ul>

                            <ul class="site-menu-sub" style="">
                                <li class="site-menu-item {{ active('students/honours*') }}">
                                    <a href="{{ route('students.honours') }}">
                                        <span class="site-menu-title">Honours</span>
                                    </a>
                                </li>
                            </ul>

                            <ul class="site-menu-sub" style="">
                                <li class="site-menu-item  {{ active('students/degree*') }}">
                                    <a href="{{ route('students.degree') }}">
                                        <span class="site-menu-title">Degree(Pass)</span>
                                    </a>
                                </li>
                            </ul>

                            @can('masters.admission.index')
                                <ul class="site-menu-sub" style="">
                                    <li class="site-menu-item {{ active('students/masters*') }}">
                                        <a href="{{ route('students.masters') }}">
                                            <span class="site-menu-title">Masters</span>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            <ul class="site-menu-sub" style="">
                                <li class="site-menu-item {{ active('students/merit-list*') }}">
                                    <a href="{{ url('students/merit-list') }}">
                                        <span class="site-menu-title">Merit List Manage</span>
                                    </a>
                                </li>
                            </ul>

                        </li>
                    @endcanany

                    @canany(['hsc_2nd_year.manage', 'hsc_2nd_year.admission', 'hsc_2nd_year.invoice'])
                        <li
                            class="site-menu-item has-sub {{ has_sub_open(['hsc/2nd/promotion*', 'hsc/2nd/promotion/invoice*']) }}">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                                <span class="site-menu-title">HSC 2nd Year</span>
                                <span class="site-menu-arrow"></span>
                            </a>

                            @can('hsc_2nd_year.admission')
                                <ul class="site-menu-sub" style="">
                                    <li class="site-menu-item  {{ active('hsc/2nd/promotion*') }}">
                                        <a href="{{ route('student.hsc.2nd.promotion.index') }}">
                                            <span class="site-menu-title">Admission</span>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('hsc_2nd_year.invoice')
                                <ul class="site-menu-sub" style="">
                                    <li class="site-menu-item  {{ active('hsc/2nd/promotion/invoice*') }}">
                                        <a href="{{ route('student.hsc.promotion.invoice') }}">
                                            <span class="site-menu-title">Invoice</span>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                        </li>
                    @endcanany

                    @canany(['student.formfillup.manage', 'honours.formfillup.manage', 'degree.formfillup.manage',
                        'masters.formfillup.manage'])
                        <li
                            class="site-menu-item has-sub {{ has_sub_open(['formfillup/honours*', 'formfillup/degree*', 'formfillup/masters*', 'formfillup/probable-list*']) }}">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                                <span class="site-menu-title">Form Fillup</span>
                                <span class="site-menu-arrow"></span>
                            </a>

                            @can('honours.formfillup.manage')
                                <ul class="site-menu-sub" style="">
                                    <li class="site-menu-item  {{ active('formfillup/honours*') }}">
                                        <a href="{{ route('student.formfillup.honours') }}">
                                            <span class="site-menu-title">Honours</span>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('degree.formfillup.manage')
                                <ul class="site-menu-sub" style="">
                                    <li class="site-menu-item  {{ active('formfillup/degree*') }}">
                                        <a href="{{ route('student.formfillup.degree') }}">
                                            <span class="site-menu-title">Degree</span>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('masters.formfillup.manage')
                                <ul class="site-menu-sub" style="">
                                    <li class="site-menu-item  {{ active('formfillup/masters*') }}">
                                        <a href="{{ route('student.formfillup.masters') }}">
                                            <span class="site-menu-title">Masters</span>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            <ul class="site-menu-sub" style="">
                                <li class="site-menu-item  {{ active('formfillup/probable-list*') }}">
                                    <a href="{{ url('formfillup/probable-list') }}">
                                        <span class="site-menu-title">Probable List Manage</span>
                                    </a>
                                </li>
                            </ul>

                        </li>
                    @endcanany


                    {{-- @canany(['student.application.manage', 'honours.application.manage', 'degree.application.manage', 'masters.application.manage']) --}}
                    <li
                        class="site-menu-item has-sub {{ has_sub_open(['application/honours*', 'application/degree*', 'application/masters*']) }}">
                        <a href="javascript:void(0)">
                            <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                            <span class="site-menu-title">Application</span>
                            <span class="site-menu-arrow"></span>
                        </a>

                        {{-- @can('honours.application.manage') --}}
                        <ul class="site-menu-sub" style="">
                            <li class="site-menu-item  {{ active('application/honours*') }}">
                                <a href="{{ route('student.application.honours') }}">
                                    <span class="site-menu-title">Honours</span>
                                </a>
                            </li>
                        </ul>
                        {{-- @endcan --}}

                        {{-- @can('degree.application.manage') --}}
                        <ul class="site-menu-sub" style="">
                            <li class="site-menu-item  {{ active('application/degree*') }}">
                                <a href="{{ route('student.application.degree') }}">
                                    <span class="site-menu-title">Degree</span>
                                </a>
                            </li>
                        </ul>
                        {{-- @endcan --}}

                        {{-- @can('masters.application.manage') --}}
                        <ul class="site-menu-sub" style="">
                            <li class="site-menu-item  {{ active('application/masters*') }}">
                                <a href="{{ route('student.application.masters') }}">
                                    <span class="site-menu-title">Masters</span>
                                </a>
                            </li>
                        </ul>
                        {{-- @endcan --}}

                    </li>
                    {{-- @endcanany --}}


                    {{-- <li class="site-menu-item {{active('admin/admission*')}}">
            <a href="{{ route('admin.admission.index') }}">
                <i class="site-menu-icon fad fa-flask" aria-hidden="true"></i>
                <span class="site-menu-title">Certificate</span>
            </a>
          </li>
        --}}

                    <li class="site-menu-item {{ active('students/idcard*') }}">
                        <a href="{{ route('students.idcard') }}">
                            <i class="site-menu-icon fad fa-plug" aria-hidden="true"></i>
                            <span class="site-menu-title">ID Card</span>
                        </a>
                    </li>

                    @canany(['certificate.testimonial.manage', 'certificate.transfer.manage', 'certificate.manage',
                        'hsc_tc_student.manage'])
                        <li
                            class="site-menu-item has-sub {{ has_sub_open(['certificates/testimonial*', 'certificates/transfer*', 'certificates/character*']) }}">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon fa fa-certificate" aria-hidden="true"></i>
                                <span class="site-menu-title">Certificates</span>
                                <span class="site-menu-arrow"></span>
                            </a>

                            @can('certificate.manage')
                                <ul class="site-menu-sub" style="">
                                    <li class="site-menu-item {{ active('certificates/testimonial*') }}">
                                        <a href="{{ route('certificates.testimonial.index') }}">
                                            <span class="site-menu-title">Testimonial</span>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('certificate.manage')
                                <ul class="site-menu-sub" style="">
                                    <li class="site-menu-item {{ active('certificates/character*') }}">
                                        <a href="{{ route('certificates.character.index') }}">
                                            <span class="site-menu-title">Character Certificate</span>
                                        </a>
                                    </li>
                                </ul>
                            @endcan

                            @can('certificate.manage')
                                <ul class="site-menu-sub" style="">
                                    <li class="site-menu-item {{ active('certificates/transfer*') }}">
                                        <a href="{{ route('certificates.transfer.index') }}">
                                            <span class="site-menu-title">Transfer Certificate</span>
                                        </a>
                                    </li>
                                </ul>
                            @endcan
                        </li>
                    @endcanany

                    {{-- <li class="site-menu-item {{active('admin/faculty*')}}">
            <a href="{{ route('admin.faculty.index') }}">
                <i class="site-menu-icon fad fa-th" aria-hidden="true"></i>
                <span class="site-menu-title">Attendence Report</span>
            </a>
          </li>
          <li class="site-menu-item {{active('admin/admission*')}}">
            <a href="{{ route('admin.admission.index') }}">
                <i class="site-menu-icon fad fa-flask" aria-hidden="true"></i>
                <span class="site-menu-title">Student Wise Attendence Report</span>
            </a>
          </li>

          <li class="site-menu-item {{active('admin/id_roll*')}}">
            <a href="{{ route('admin.id_roll.create') }}">
                <i class="site-menu-icon fad fa-flask" aria-hidden="true"></i>
                <span class="site-menu-title">Class Wise Attendence Report</span>
            </a>
          </li> --}}
                    @can('hsc_tc_student.manage')
                        <li class="site-menu-item {{ active('students/hsc/hscTcStudents*') }}">
                            <a href="{{ route('students.hsc.hsctcstudents') }}">
                                <i class="site-menu-icon fad fa-flask" aria-hidden="true"></i>
                                <span class="site-menu-title">HSC TC Student</span>
                            </a>
                        </li>
                    @endcan

                    @can('admission.manage')
                        <li
                            class="site-menu-item has-sub {{ has_sub_open(['students/migration*', 'students/migration/migrationTable*']) }}">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                                <span class="site-menu-title">Student Migration</span>
                                <span class="site-menu-arrow"></span>
                            </a>

                            <ul class="site-menu-sub" style="">
                                <li class="site-menu-item {{ active('students/migration/migrationList*') }}">
                                    <a href="{{ route('students.migration.list') }}">
                                        <span class="site-menu-title">Migrated List</span>
                                    </a>
                                </li>
                            </ul>

                            <ul class="site-menu-sub" style="">
                                <li class="site-menu-item {{ active('students/migration/migrationTable*') }}">
                                    <a href="{{ route('students.migration.table') }}">
                                        <span class="site-menu-title">Migrations</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    <li
                        class="site-menu-item has-sub {{ has_sub_open(['students/report/hsc*', 'students/report/honours*', 'students/report/masters*', 'students/report/degree*']) }}">
                        <a href="javascript:void(0)">
                            <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                            <span class="site-menu-title">Reports</span>
                            <span class="site-menu-arrow"></span>
                        </a>

                        <ul class="site-menu-sub" style="">
                            <li class="site-menu-item {{ active('students/report/hsc*') }}">
                                <a href="{{ route('students.report.hsc') }}">
                                    <span class="site-menu-title">HSC</span>
                                </a>
                            </li>
                        </ul>

                        <ul class="site-menu-sub" style="">
                            <li class="site-menu-item {{ active('students/report/honours*') }}">
                                <a href="{{ route('students.report.honours') }}">
                                    <span class="site-menu-title">Honours</span>
                                </a>
                            </li>
                        </ul>

                        <ul class="site-menu-sub" style="">
                            <li class="site-menu-item  {{ active('students/report/degree*') }}">
                                <a href="{{ route('students.report.degree') }}">
                                    <span class="site-menu-title">Degree(Pass)</span>
                                </a>
                            </li>
                        </ul>

                        @can('masters.admission.index')
                            <ul class="site-menu-sub" style="">
                                <li class="site-menu-item {{ active('students/report/masters*') }}">
                                    <a href="{{ route('students.report.masters') }}">
                                        <span class="site-menu-title">Masters</span>
                                    </a>
                                </li>
                            </ul>
                        @endcan

                        @can('student.fees-payment.control')
                            <ul class="site-menu-sub" style="">
                                <li class="site-menu-item {{ active('students/fees-payment*') }}">
                                    <a href="{{ route('student.fees-payment.report') }}">
                                        <span class="site-menu-title">Fees Payment</span>
                                    </a>
                                </li>
                            </ul>
                        @endcan

                    </li>

                </ul>

            </div>
        </div>
    </div>
</div>
