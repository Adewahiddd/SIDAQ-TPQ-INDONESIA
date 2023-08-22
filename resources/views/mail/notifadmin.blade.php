<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration</title>
</head>
<body>
    <h2>New User Registration</h2>
    <p>A new user has registered:</p>
    <p><strong>Name:</strong> {{ $user->nama_masjid }}</p>
    <p><strong>Gambar:</strong> <img src="{{ asset($user->gambar) }}" alt="User Gambar"></p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Provinsi:</strong> {{ $user->provinsi }}</p>
    <p><strong>Kabupaten:</strong> {{ $user->kabupaten }}</p>
    <p><strong>Alamat Masjid:</strong> {{ $user->alamat_masjid }}</p>
</body>
</html>
