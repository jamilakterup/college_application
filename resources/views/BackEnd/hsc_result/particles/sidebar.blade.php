<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-category">HSC Result</li>
                    @can('class_setup.view')
                        <li class="site-menu-item {{ active('hsc_result/class', 'hsc_result/group') }}">
                            <a href="{{ route('hsc_result.class.index') }}">
                                <i class="site-menu-icon fa fa-book fa-fw" aria-hidden="true"></i>
                                <span class="site-menu-title">Class Setup</span>
                            </a>
                        </li>
                    @endcan

                    @can('subject_setup.view')
                        <li
                            class="site-menu-item {{ active('hsc_result/subject', 'hsc_result/subject/*', 'hsc_result/assign_subject*') }}">
                            <a href="{{ route('hsc_result.subject.index') }}">
                                <i class="site-menu-icon fa fa-book fa-fw" aria-hidden="true"></i>
                                <span class="site-menu-title">Subject Setup</span>
                            </a>
                        </li>
                    @endcan

                    @can('exam_setup.view')
                        <li class="site-menu-item {{ active('hsc_result/exam*', 'hsc_result/class_test*') }}">
                            <a href="{{ route('hsc_result.exam.index') }}">
                                <i class="site-menu-icon fa fa-book fa-fw" aria-hidden="true"></i>
                                <span class="site-menu-title">Exam Setup</span>
                            </a>
                        </li>
                    @endcan

                    @can('subject_info.view')
                        <li
                            class="site-menu-item {{ active('hsc_result/subject_info*', 'hsc_result/student_subject_assign*') }}">
                            <a href="{{ route('hsc_result.subject_info.index') }}">
                                <i class="site-menu-icon fa fa-sitemap" aria-hidden="true"></i>
                                <span class="site-menu-title">Student Subject Info</span>
                            </a>
                        </li>
                    @endcan

                    @can('mark_input.view')
                        <li class="site-menu-item {{ active('hsc_result/mark_input*') }}">
                            <a href="{{ route('hsc_result.mark_input.index') }}">
                                <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                                <span class="site-menu-title">Mark Input</span>
                            </a>
                        </li>
                    @endcan

                    @can('result_processing.view')
                        <li class="site-menu-item {{ active('hsc_result/process*') }}">
                            <a href="{{ route('hsc_result.process.index') }}">
                                <i class="site-menu-icon fa fa-calculator" aria-hidden="true"></i>
                                <span class="site-menu-title">Result Proccessing</span>
                            </a>
                        </li>
                    @endcan

                    @can('result_publish.view')
                        <li class="site-menu-item {{ active('hsc_result/result_publish*') }}">
                            <a href="{{ route('hsc_result.result_publish.index') }}">
                                <i class="site-menu-icon fa fa-bullhorn" aria-hidden="true"></i>
                                <span class="site-menu-title">Result Publish</span>
                            </a>
                        </li>
                    @endcan

                    @can('admit_card.generate')
                        <li class="site-menu-item {{ active('hsc_result/admit_card*') }}">
                            <a href="{{ route('hsc_result.admit_card.index') }}">
                                <i class="site-menu-icon fa fa-credit-card" aria-hidden="true"></i>
                                <span class="site-menu-title">Admit Card Generate</span>
                            </a>
                        </li>
                    @endcan

                    @can('transcript.generate')
                        <li class="site-menu-item {{ active('hsc_result/transcript*') }}">
                            <a href="{{ route('hsc_result.transcript.index') }}">
                                <i class="site-menu-icon fa fa-file" aria-hidden="true"></i>
                                <span class="site-menu-title">Transcript Generate</span>
                            </a>
                        </li>
                    @endcan

                    @can('sticker.generate')
                        <li class="site-menu-item {{ active('hsc_result/sticker*') }}">
                            <a href="{{ route('hsc_result.sticker.index') }}">
                                <i class="site-menu-icon fa fa-certificate" aria-hidden="true"></i>
                                <span class="site-menu-title">Sticker Generate</span>
                            </a>
                        </li>
                    @endcan

                    @can('attendance_sheet.generate')
                        <li class="site-menu-item {{ active('hsc_result/attendance_sheet*') }}">
                            <a href="{{ route('hsc_result.attendance_sheet.index') }}" title="Attendence Sheet Generate">
                                <i class="site-menu-icon fa fa-spinner" aria-hidden="true"></i>
                                <span class="site-menu-title">Attendence Sheet Generate</span>
                            </a>
                        </li>
                    @endcan

                    @can('exam_date.setup')
                        <li class="site-menu-item {{ active('hsc_result/exam_date*') }}">
                            <a href="{{ route('hsc_result.exam_date.index') }}">
                                <i class="site-menu-icon fa fa-bolt" aria-hidden="true"></i>
                                <span class="site-menu-title">Exam Date Setup</span>
                            </a>
                        </li>
                    @endcan

                    @can('result_reporting.view')
                        <li class="site-menu-item {{ active('hsc_result/result_reporting*') }}">
                            <a href="{{ route('hsc_result.result_reporting') }}">
                                <i class="site-menu-icon fa fa-bolt" aria-hidden="true"></i>
                                <span class="site-menu-title">Result Reporting</span>
                            </a>
                        </li>
                    @endcan

                    @can('progress_report.view')
                        <li class="site-menu-item {{ active('hsc_result/progress_report*') }}">
                            <a href="{{ route('hsc_result.progress_report') }}">
                                <i class="site-menu-icon fa fa-bolt" aria-hidden="true"></i>
                                <span class="site-menu-title">Progress Report</span>
                            </a>
                        </li>
                    @endcan

                    @can('progress_report.view')
                        <li class="site-menu-item {{ active('hsc_result/fees_details*') }}">
                            <a href="{{ route('hsc_result.fees_details') }}">
                                <i class="site-menu-icon fa fa-bolt" aria-hidden="true"></i>
                                <span class="site-menu-title">Fees Details</span>
                            </a>
                        </li>
                    @endcan

                    @can('result_upload.view')
                        <li class="site-menu-item {{ active('pre/hsc_result*') }}">
                            <a href="{{ route('pre_hsc_result.create') }}">
                                <i class="site-menu-icon fad fa-flask" aria-hidden="true"></i>
                                <span class="site-menu-title">HSC Result Upload</span>
                            </a>
                        </li>
                    @endcan

                </ul>

            </div>
        </div>
    </div>
</div>
