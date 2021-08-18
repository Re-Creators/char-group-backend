<html>
<head>
  <meta charset="utf-8">
  <title>{{ config('app.name') }}</title>
  <script>
    window.opener.postMessage({token : "{{ $token }}"}, "{{ env('FRONTEND_URL') }}")
    window.close()
  </script>
</head>
<body>
</body>
</html>