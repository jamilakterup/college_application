<!DOCTYPE html>
<html>

<head>
    <title>Fees Details - {{ $year_type }}</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: DejaVu Sans, sans-serif;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Fees Details for Year: {{ $year_type }} {{ $science_students->count() }}</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Session</th>
                <th>Current Year</th>
                <th>Fees Header</th>
                <th>Type</th>
                <th>Science Amount</th>
                <th>Humanities Amount</th>
                <th>Business Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fees_details as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->session }}</td>
                    <td>{{ $item->current_year }}</td>
                    <td>{{ $item->fees_header }}</td>
                    <td>{{ $item->is_gov == '0' ? 'Government Fees' : 'Non-Gov Fees' }}</td>
                    <td>{{ $item->science }}</td>
                    <td>{{ $item->humanities }}</td>
                    <td>{{ $item->business }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
