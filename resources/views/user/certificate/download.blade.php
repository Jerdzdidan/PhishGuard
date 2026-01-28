<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate - {{ $user->first_name }} {{ $user->last_name }}</title>
</head>
<body>
    <script>
        // This page will trigger the PDF generation
        window.location.href = '{{ route("certificate.generate") }}';
    </script>
    
    <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
        <h2>Generating your certificate...</h2>
        <p>Your download will start automatically.</p>
        <p>If the download doesn't start, <a href="{{ route('certificate.generate') }}">click here</a>.</p>
    </div>
</body>
</html>
