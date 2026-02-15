<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title>My Profile</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

</head>

<body style="background:#337ab7">

    <div class="container mt-5" style="max-width:900px">


        <div class="panel panel-default">

            <div class="panel-heading text-center bg-primary text-white p-4">

                <h3>🎓 My Graduation Profile</h3>
                <p>Your submitted academic information</p>

            </div>


            <div class="panel-body p-4">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- <div class="alert alert-warning">
                    User ID is: <strong>{{ $info->user_id }}</strong> <br>
                    Remember this ID for future reference and editing your profile.
                </div> --}}

                <table class="table table-bordered">

                    <tr>
                        <th>Name</th>
                        <td>{{ $info->name }}</td>
                    </tr>

                    <tr>
                        <th>Father's Name</th>
                        <td>{{ $info->father_name }}</td>
                    </tr>

                    <tr>
                        <th>Mother's Name</th>
                        <td>{{ $info->mother_name }}</td>
                    </tr>

                    <tr>
                        <th>Class Roll</th>
                        <td>{{ $info->class_roll }}</td>
                    </tr>

                    <tr>
                        <th>HSC Roll</th>
                        <td>{{ $info->hsc_roll }}</td>
                    </tr>

                    <tr>
                        <th>Session</th>
                        <td>{{ $info->session }}</td>
                    </tr>

                    <tr>
                        <th>Institution</th>
                        <td>{{ $info->institution_name }}</td>
                    </tr>

                    <tr>
                        <th>Mobile</th>
                        <td>{{ $info->mobile }}</td>
                    </tr>

                    <tr>
                        <th>Photo</th>
                        <td>
                            @if ($info->photo)
                                <img src="{{ asset($info->photo) }}" width="140" class="rounded">
                            @endif
                        </td>
                    </tr>

                </table>


                <div class="text-center mt-4">

                    <a href="{{ route('graduated_student.edit', ['user_id' => $info->user_id]) }}"
                        class="btn btn-warning">
                        ✏️ Edit Profile
                    </a>

                    <a href="{{ route('graduated_student.form', ['user_id' => $info->user_id]) }}"
                        class="btn btn-success">
                        Back to Form
                    </a>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
