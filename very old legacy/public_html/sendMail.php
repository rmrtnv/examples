<?php

$to      = 'to@mail.com';
$subject = 'subject';
$message = 'hello';
$headers = 'From: admin@mail.site' . "\r\n" .
    'Reply-To: admin@mail.site' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?> 