<html>
<head>
  <meta charset="utf-8">
  <title>{{ config('app.name') }}</title>
  <script>
    window.opener.postMessage({token : "{{ $token }}"}, "http://localhost:8081/")
    window.close()
  </script>
</head>
<body>
</body>
</html>