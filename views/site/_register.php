<?php

if ($msg['key'] === 'msgRequestSuccess' || $msg['key'] === 'msgRequestFail') {
    $msgRegistration = null;
    $msgRequest = $msg;
} else {
    $msgRegistration = $msg;
    $msgRequest = null;
}


$content = $this->render('blocks/_registration-block', [
    'user' => $user,
    'staticDBsContent' => $staticDBsContent,
    'msg' => $msgRegistration
]);

echo $this->render('register', [
    'content' => $content,
]);