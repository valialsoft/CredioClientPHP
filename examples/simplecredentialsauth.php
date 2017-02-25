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

if (isset($_POST, $_POST['uname'], $_POST['upass'], $_POST['domain'])) {//user credentials are provided, try to authenticate
    $credio = Credio\Client::getInstance($_POST['domain']);

    try {
        $credio->authenticate($_POST['uname'], $_POST['upass']);
        $authenticated = "provided valid credentials for ".$_POST['uname'];
    } catch (Credio\Exception $e) {//error occurred. becouse this is an example we only get error message and disply it.
        $error_text = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Simple Credentials Authentication</title>
</head>
<body>
    <header>
        <h1>Simple Credentials Authentication</h1>
    </header>
<?php if (isset($authenticated)) { ?>
    <div><?php echo($authenticated); ?></div>
<?php } else { ?>
    <div>Credentials for Authentication</div>
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
    <div>Error:&nbsp;<span style="color: red;"><?php echo($error_text); ?></span></div>
<?php } ?>
</body>
</html>