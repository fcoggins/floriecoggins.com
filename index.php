<?php

require 'vendor/autoload.php';
date_default_timezone_set('America/New_York');

// use Monolog\Logger;
// use Monolog\Handler\StreamHandler;

// $log = new Logger('name');
// $log->pushHandler(new StreamHandler('app.log', Logger::WARNING));
// $log->addWarning('Oh my!!!');
//echo "Hello World";

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig()
));

$app->add(new \Slim\Middleware\SessionCookie(array(
    'expires' => '20 minutes',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'httponly' => false,
    'name' => 'slim_session',
    'secret' => 'CHANGE_ME',
    'cipher' => MCRYPT_RIJNDAEL_256,
    'cipher_mode' => MCRYPT_MODE_CBC
)));

$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
);
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

$app->get('/', function() use($app){
    $app->render('home.twig');
})->name('home');

$app->get('/photos', function() use($app){
    $app->render('photos.twig');
})->name('photos');

$app->get('/about', function() use($app){
    $app->render('about.twig');
})->name('about');

$app->get('/contact', function() use($app){
    $app->render('contact.twig');
})->name('contact');

$app->post('/contact', function() use($app){
    $name = $app->request->post('name');
    $email = $app->request->post('email');
    $message = $app->request->post('msg');

    if (!empty($name) && !empty($email) && !empty($message)){
        $cleanName = filter_var($name, FILTER_SANITIZE_STRING);
        $cleanEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        $cleanMessage = filter_var($message, FILTER_SANITIZE_STRING);
        echo $cleanName;
    } else {
        $app->flash('error', 'Name, email and message are required');
        $app->redirect('/phpsite/contact');    
    }

    $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
    $mailer = Swift_Mailer::newInstance($transport);
    $message = Swift_Message::newInstance();
    $message->setSubject('Email from Website');
    $message->setFrom(array(
            $cleanEmail => $cleanName
        ));
    $message->setTo(array('florie.coggins@gmail.com'));
    $message->setBody($cleanMessage);

    $result = $mailer->send($message);

    if($result > 0){
        $app->flash('success', 'Thank you. Your message has been sent.');
        $app->redirect('/phpsite/contact');
    } else {
        $app->flash('error', 'There was a problem. Email could not be sent.');
        $app->redirect('/phpsite/contact');
    }
});

$app->run();

?>