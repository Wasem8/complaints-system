<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Complaints Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>Complaints Report</h1>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Type</th>
        <th>Status</th>
        <th>Department</th>
        <th>Created At</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $complaint)
        <tr>
            <td>{{ $complaint->id }}</td>
            <td>{{ $complaint->type }}</td>
            <td>{{ ucfirst($complaint->status) }}</td>
            <td>{{ $complaint->department->name ?? '-' }}</td>
            <td>{{ $complaint->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
