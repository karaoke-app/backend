<!DOCTYPE html>
<html>
<head>
    <title>Aktywacja konta</title>
</head>

<body>
<h2>Cześć, {{$user['name']}}</h2>
<br/>
Właśnie dostaliśmy informację o tym, że zarejestrowałeś się do naszego serwisu Karaoke. Aby korzystać z tego konta w serwisie, musisz kliknąć w link aktywacyjny znajdujący się poniżej.
<br/>
<a href="{{route('activate', $user->id)}}">Verify Email</a>
</body>

</html>
