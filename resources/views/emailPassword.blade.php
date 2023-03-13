<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8"><title>{{$mailMessage->title}}</title>
</head>
<body>
Dear {{$mailMessage->recieverName}} !
<p>Thank You for contacting us. We will revert back on your query
    with in 24 Hours.</p>
<p></p>
Thank You,
<br/>
{{ $mailMessage->sender }}
<br/>
{{ $mailMessage->senderCompany}}
</body>
</html>
