#!/usr/bin/env php
<?php

use Moccalotto\Ssh\Auth;
use Moccalotto\Ssh\Session;
use Moccalotto\Ssh\Connect;
use Moccalotto\Ssh\Terminal;

require 'vendor/autoload.php';

$ip = '127.0.0.1';
$username = 'my_username';
$port = 22;
$pubkeyfile = '/path/to/my/key/id_rsa';
$privkeyfile = 'path/to/my/key/id_rsa.pub';
$keypass = 'my_password';


/*
| Create an SSH session
|-----------------------
| Connect to specified IP and port, and authorize via SSH key.
| You can authorize via password by calling Auth::viaPassword($username, $password)
*/

$ssh = new Session(
    Connect::to($ip, $port),
    Auth::viaKeyFile($username, $pubkeyfile, $privkeyfile, $keypass)
);


/*
| Create terminal settings
|--------------------------
| This step is optional.
| You do not need to call $ssh-withTerminal()
| If you don't. Default terminal settings will be used.
*/
$terminal = Terminal::create()
    ->width(80, 'chars')
    ->height(25, 'chars');


/* 
| Execute a single command on the remote server 
| ----------------------------------------------
| Capture its output and echo it on the local screen.
*/
echo $ssh->withTerminal($terminal)->execute('echo $HOME $PWD');



/*
| Open a shell on the remote server
|--------------------------
| Open a shell. 
| Execute a few commands.
| Logout.
| Print the output from the shell.
| The shell is automatically closed when callback returns.
*/
echo $ssh->withTerminal($terminal)->shell(function($shell) {
    
    $captured_output = $shell
        ->writeline('echo The home dir is: $HOME')
        ->writeline('echo the contents of $PWD is:; ls -lah')
        ->writeline('logout')
        ->wait(0.3) // give the shell time to execute the commands.
        ->readToEnd();

    return $captured_output;

});
