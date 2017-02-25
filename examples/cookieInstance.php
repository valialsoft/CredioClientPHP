<?php
/*
MIT License

Copyright (c) 2016 Valialsoft, LLC

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

require_once '../CredioClient.php';

if (isset($_GET, $_GET['source']))
{
    show_source(__FILE__); die();
}

function tnf($inst)
{
    echo("handle !!!\n");
}
$error_text = null;
$domain = isset($_POST['domain']) ? $_POST['domain'] : "CREDIO";

$cl = Credio\Client::getCookieInstance($domain, $_COOKIE);
$cl->setCallback(\Credio\ErrorCodes::TOKEN_NOT_FOUND, 'tnf');

if (!$cl->haveToken()) {//no token yet, we must aquire user credentials and try to get token
    if (isset($_POST['uname']) && isset($_POST['upass'])) {//user credentials are provided, try to get token
        try {
            $token = $cl->getToken($_POST['uname'], $_POST['upass']);
            //horrey! we have a token
        } catch (Credio\Exception $e) {//error occurred. becouse this is an example we only get error message and disply it.
            $error_text = $e->getMessage();
        }
    }
} else {
    if (isset($_POST['logout'])) {
        try {
            $cl->releaseToken();
        } catch (Credio\Exception $e) {
            if ($e->getCode() != 0x101)//don't care if token was not found (error code 0x101)
                $error_text = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Simple Credentials Authentication with Cookie</title>
</head>
<body>
    <header>
        <h1>Simple Credentials Authentication with Cookie</h1>
    </header>
<?php if ($cl->haveToken()) { ?>
    <div>Acquired token: <?php echo($cl->token); ?></div>
    <form method="post" target="_self">
        <input type="submit" id="logout" name="logout" value="logout"/>
    </form>
<?php } else { ?>
    <div>Login to obtain token.</div>
    <div>
        <form method="post" target="_self">
            <label for="uname">Username</label><input id="uname" name="uname" type="text" value="Administrator"/><br/>
            <label for="upass">Password</label><input id="upass" name="upass" type="password" value="password"/><br/>
            <label for="domain">Domain</label><input id="domain" name="domain" type="text" value="CREDIO"/><br/>
            <input type="submit" id="subm" name="subm" value="login"/>
        </form>
    </div>
<?php } ?>
<?php if (isset($error_text)) { ?>
    <div>Error: <font color="red"><?php echo($error_text); ?></font></div>
<?php } ?>
</body>
</html>