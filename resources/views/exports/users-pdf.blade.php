<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 10px 0; }
        .meta { font-size: 11px; color: #6b7280; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f9fafb; font-weight: 600; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <h1>Users</h1>
    <div class="meta">
        Generated at: {{ now()->format('Y-m-d H:i:s') }}
        @if(!empty($search))
            <span class="muted">| Filter: "{{ $search }}"</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>Name</th>
                <th>Email</th>
                <th style="width: 120px;">Role</th>
                <th style="width: 110px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
