<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        h2 {
            margin: 0 0 8px;
            font-size: 18px;
        }
        .meta {
            margin-bottom: 12px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: right;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            font-weight: 700;
        }
        .empty {
            text-align: center;
            color: #6b7280;
            padding: 12px;
        }
    </style>
</head>
<body>
<h2>{{ $title }}</h2>
<p class="meta">تاريخ الإنشاء: {{ $generatedAt }}</p>

<table>
    <thead>
    <tr>
        @foreach($columns as $column)
            <th>{{ $column }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $row)
        <tr>
            @foreach($row as $cell)
                <td>{{ $cell }}</td>
            @endforeach
        </tr>
    @empty
        <tr>
            <td class="empty" colspan="{{ count($columns) }}">لا توجد بيانات</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>

