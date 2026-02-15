<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title>Edit Profile</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

</head>

<body style="background:#337ab7">


    <div class="container mt-4" style="max-width:85%;">


        <div class="panel panel-default">


            <div class="panel-heading text-center bg-warning p-4">

                <h3>✏️ Edit Graduation Profile</h3>

                <p>Update your information carefully</p>

            </div>


            <div class="panel-body p-4">


                <form action="{{ route('graduated_student.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf


                    <div class="row">


                        <div class="col-md-6 form-group mb-3">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ $info->name }}" class="form-control"
                                required>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label>Father's Name</label>
                            <input type="text" name="father_name" value="{{ $info->father_name }}"
                                class="form-control" required>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label>Mother's Name</label>
                            <input type="text" name="mother_name" value="{{ $info->mother_name }}"
                                class="form-control" required>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label>Class Roll</label>
                            <input type="text" name="class_roll" value="{{ $info->class_roll }}" class="form-control"
                                required>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label>HSC Roll</label>
                            <input type="text" name="hsc_roll" value="{{ $info->hsc_roll }}" class="form-control"
                                required>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label>Session</label>

                            <select name="session" class="form-control">

                                <option {{ $info->session == '2022-2023' ? 'selected' : '' }}>
                                    2022-2023
                                </option>

                                <option {{ $info->session == '2023-2024' ? 'selected' : '' }}>
                                    2023-2024
                                </option>

                            </select>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label>Institution</label>

                            <input type="text" name="institution_name" value="{{ $info->institution_name }}"
                                class="form-control" required>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label>Mobile</label>

                            <input type="text" name="mobile" value="{{ $info->mobile }}" class="form-control"
                                required>
                        </div>


                        <div class="col-md-6 form-group mb-3">
                            <label>Photo</label>

                            <input type="file" name="photo" class="form-control">

                            <img src="{{ asset($info->photo) }}" width="100" class="mt-2 rounded">
                        </div>


                    </div>


                    <div class="text-center mt-4">

                        <button class="btn btn-success btn-lg">

                            💾 Update Information

                        </button>

                        <a href="{{ route('graduated_student.view', ['user_id' => $info->user_id]) }}"
                            class="btn btn-secondary btn-lg">

                            ⬅ Back

                        </a>

                    </div>


                </form>

            </div>

        </div>

    </div>

</body>

</html>
