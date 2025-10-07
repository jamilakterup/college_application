<ul class="nav nav-tabs" id="teacherDetailTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="personal-tab" data-toggle="tab" data-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">Personal</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="education-tab" data-toggle="tab" data-target="#education" type="button" role="tab" aria-controls="education" aria-selected="false">Education</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="employment-tab" data-toggle="tab" data-target="#employment" type="button" role="tab" aria-controls="employment" aria-selected="false">Employment</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="appointment-tab" data-toggle="tab" data-target="#appointment" type="button" role="tab" aria-controls="appointment" aria-selected="false">Appointment</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="career-tab" data-toggle="tab" data-target="#career" type="button" role="tab" aria-controls="career" aria-selected="false">Career</button>
    </li>
  </ul>

  <div class="tab-content" id="teacherDetailTabContent" style="margin-top: 1.5rem;">
    <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
        @include('BackEnd.teacher.teacher.detail_particles.personal')
    </div>

    <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
        @includeIf('BackEnd.teacher.teacher.detail_particles.education')
    </div>

    <div class="tab-pane fade" id="employment" role="tabpanel" aria-labelledby="employment-tab">
        @includeIf('BackEnd.teacher.teacher.detail_particles.employment')
    </div>

    <div class="tab-pane fade" id="appointment" role="tabpanel" aria-labelledby="appointment-tab">
        @includeIf('BackEnd.teacher.teacher.detail_particles.appointment')
    </div>

    <div class="tab-pane fade" id="career" role="tabpanel" aria-labelledby="career-tab">
        @includeIf('BackEnd.teacher.teacher.detail_particles.career')
    </div>

  </div>