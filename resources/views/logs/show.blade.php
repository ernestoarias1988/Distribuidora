<!DOCTYPE html>
<html>
<head>
    <title>Logs</title>
</head>
<body>
    <h1>Application Logs</h1>

    @if(session('status'))
        <p style="color:green">{{ session('status') }}</p>
    @endif

    <form action="{{ route('logs.archive') }}" method="POST" style="margin-bottom:1rem">
        @csrf
        <button type="submit">CreateNew</button>
    </form>

    <pre>{{ $logs }}</pre>
</body>
</html>