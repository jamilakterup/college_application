<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HSC Result</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <style>
        body {
            background-color: #337ab7;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        .panel {
            border-radius: 6px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .panel-heading {
            background-color: #337ab7;
            color: #fff;
            text-align: center;
            padding: 20px;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }

        .panel-heading h3 {
            margin-top: 0;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .panel-heading p {
            margin-bottom: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .panel-body {
            padding: 30px;
        }

        label {
            font-weight: 600;
            font-size: 13px;
        }

        .form-control {
            border-radius: 4px;
            height: 38px;
        }

        .form-control:focus {
            border-color: #337ab7;
            box-shadow: 0 0 6px rgba(51, 122, 183, 0.35);
        }

        .photo-preview {
            margin-top: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 3px;
        }

        .btn-success {
            padding: 10px 40px;
            font-weight: 600;
            border-radius: 25px;
        }

        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: #337ab7;
            border-left: 4px solid #337ab7;
            padding-left: 10px;
            margin-bottom: 20px;
        }
    </style>

</head>

<body>
    <div class="container" style="max-width:85%; margin-top:30px;">

        <div class="panel panel-default" style="border: 1px solid #c2c2c2;">

            <div class="panel-heading" style="background-color: #03d4a7">
                <h3>🎓 Welcome Graduate!</h3>
                <p>Please Complete Your Academic Profile</p>
            </div>

            <div class="panel-body">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('graduated_student.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="section-title">📘 Personal Information</div>

                    <div class="row">

                        <div class="col-md-6 form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $info->name ?? '') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Father's Name</label>
                            <input type="text" name="father_name" class="form-control"
                                value="{{ old('father_name', $info->father_name ?? '') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control"
                                value="{{ old('mother_name', $info->mother_name ?? '') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Class Roll (College)</label>
                            <input type="text" name="class_roll" class="form-control"
                                value="{{ old('class_roll', $info->class_roll ?? '') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>HSC Roll</label>
                            <input type="text" name="hsc_roll" class="form-control"
                                value="{{ old('hsc_roll', $info->hsc_roll ?? '') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Session</label>
                            <select name="session" class="form-control" required>
                                <option value="">-- Select Session --</option>
                                <option value="2022-2023"
                                    {{ old('session', $info->session ?? '') == '2022-2023' ? 'selected' : '' }}>
                                    2022-2023
                                </option>
                                <option value="2023-2024"
                                    {{ old('session', $info->session ?? '') == '2023-2024' ? 'selected' : '' }}>
                                    2023-2024
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Institution Name</label>
                            <input type="text" name="institution_name" class="form-control"
                                value="{{ old('institution_name', $info->institution_name ?? '') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" class="form-control"
                                value="{{ old('mobile', $info->mobile ?? '') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Photo</label>
                            <input type="file" name="photo" class="form-control">
                            @if (!empty($info->photo))
                                <img src="{{ asset('storage/' . $info->photo) }}" class="photo-preview" width="120">
                            @endif
                        </div>

                    </div>

                    <div class="text-center" style="margin-top:30px;">
                        <button class="btn btn-success btn-lg">
                            💾 Save Information
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>

</html>
