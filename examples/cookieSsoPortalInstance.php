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

if (isset($_GET, $_GET['source'])) {
    show_source(__FILE__);
    die();
}

$credio = Credio\Client::getCookieSsoPortalInstance(Credio\Client::defaultDomain, $_COOKIE);
//script will not continue until valid token is provided.

if (isset($_POST, $_POST['logout'])) {
    try {
        $credio->releaseToken();
    } catch (Credio\Exception $e) {
    }

    header("Location: #");
    die("your browser does not support redirect.");
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SSO Portal Credentials Authentication with Cookie</title>
</head>
<body>

<header>
    <h1>SSO Portal Credentials Authentication with Cookie</h1>
</header>

<div>Acquired token: <?php echo($credio->token); ?></div>
<form method="post" target="_self">
    <input type="submit" id="logout" name="logout" value="logout"/>
</form>

</body>
</html>