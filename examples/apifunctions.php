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

$domain = isset($_POST['domain']) ? $_POST['domain'] : "CREDIO";

$authenticate_error = NULL;
$authenticate_text = NULL;

$getToken_error = NULL;
$getToken_text = NULL;

$isValidToken_error = $isValidToken_text = NULL;
$releaseToken_error = $releaseToken_text = NULL;

$credio = Credio\Client::getInstance($domain);

if (isset($_POST['submit_authenticate'])) {
    try {
        $credio->authenticate($_POST['uname'], $_POST['upass']);
        $authenticate_text = "user " . $_POST['uname'] . " successfully authenticated";
    } catch (Credio\Exception $e) {
        $authenticate_error = "Code:" . $e->getCode() . ", Msg:'" . $e->getMessage() . "'";
    }
}
if (isset($_POST['submit_getToken'])) {
    try {
        $t = $credio->getToken($_POST['uname'], $_POST['upass']);
        $getToken_text = "Token: " . $t->__toString() . " received";
        $_POST['token'] = $t->__toString();
    } catch (Credio\Exception $e) {
        $getToken_error = "Code:" . $e->getCode() . ", Msg:'" . $e->getMessage() . "'";
    }
}
if (isset($_POST['submit_isValidToken'])) {
    try {
        $credio->isValidToken(new Credio\Token($_POST['token']));
        $isValidToken_text = "Token: " . $_POST['token'] . " is valid.";
    } catch (Credio\Exception $e) {
        $isValidToken_error = "Code:" . $e->getCode() . ", Msg:'" . $e->getMessage() . "'";
    }
}
if (isset($_POST['submit_releaseToken'])) {
    try {
        $credio->releaseToken(new Credio\Token($_POST['token']));
        $releaseToken_text = "Token: " . $_POST['token'] . " successfully released.";
    } catch (Credio\Exception $e) {
        $releaseToken_error = "Code:" . $e->getCode() . ", Msg:'" . $e->getMessage() . "'";
    }
}
if (isset($_POST['submit_getPermissions'])) {
    try {
        $perms = $credio->getPermissions($_POST['path'], new Credio\Token($_POST['token']));
        $getPermissions_text = $perms;
    } catch (Credio\Exception $e) {
        $getPermissions_error = "Code:" . $e->getCode() . ", Msg:'" . $e->getMessage() . "'";
    }
}
if (isset($_POST['submit_getUserPermissions'])) {
    try {
        $perms = $credio->getPermissions($_POST['path'], null, $_POST['uname']);
        $getUserPermissions_text = $perms;
    } catch (Credio\Exception $e) {
        $getUserPermissions_error = "Code:" . $e->getCode() . ", Msg:'" . $e->getMessage() . "'";
    }
}
if (isset($_POST['submit_getAttributes'])) {
    try {
        $perms = $credio->getAttributes($_POST['path'], new Credio\Token($_POST['token']));
        $getAttributes_text = $perms;
    } catch (Credio\Exception $e) {
        $getAttributes_error = "Code:" . $e->getCode() . ", Msg:'" . $e->getMessage() . "'";
    }
}
if (isset($_POST['submit_getDomainAttributes'])) {
    try {
        $perms = $credio->getAttributes('');
        $getDomainAttributes_text = $perms;
    } catch (Credio\Exception $e) {
        $getDomainAttributes_error = "Code:" . $e->getCode() . ", Msg:'" . $e->getMessage() . "'";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Credio Client - PHP Examples / API Functions</title>
    <style>
        article.for {
            border: 1px dashed black;
            display: inline-block;
            padding: 5px;
            border-radius: 5px;
            width: 300px;
            min-height: 200px;
            vertical-align: top;
            height: auto;
        }
    </style>
</head>
<body>

<header>
    <h1>Credio Client - PHP Examples / API Functions</h1>
</header>

<section>
    <header><h2>Authentication API</h2></header>

    <article class="for">
        <header><h3>authenticate</h3></header>

        <form method="post" target="_self" action="apifunctions.php">
            <table>
                <tr>
                    <th>Username</th>
                    <td><input id="uname" name="uname" type="text"
                               value="<?php echo(isset($_POST['uname']) ? $_POST['uname'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><input id="upass" name="upass" type="password"
                               value="<?php echo(isset($_POST['upass']) ? $_POST['upass'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td><input id="domain" name="domain" type="text" value="<?php echo($domain); ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" id="submit_authenticate" name="submit_authenticate"
                                           value="authenticate"/></td>
                </tr>
            </table>
        </form>

        <?php if (isset($authenticate_error)) { ?>
            <div>Error: <font color="red"><?php echo($authenticate_error); ?></font></div>
        <?php } ?>
        <?php if (isset($authenticate_text)) { ?>
            <div>Info: <font color="green"><?php echo($authenticate_text); ?></font></div>
        <?php } ?>
    </article>

    <article class="for">
        <header><h3>getToken</h3></header>

        <form method="post" target="_self" action="apifunctions.php">
            <table>
                <tr>
                    <th>Username</th>
                    <td><input id="uname" name="uname" type="text"
                               value="<?php echo(isset($_POST['uname']) ? $_POST['uname'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><input id="upass" name="upass" type="password"
                               value="<?php echo(isset($_POST['upass']) ? $_POST['upass'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td><input id="domain" name="domain" type="text" value="<?php echo($domain); ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" id="submit_getToken" name="submit_getToken" value="getToken"/>
                    </td>
                </tr>
            </table>
        </form>

        <?php if (isset($getToken_error)) { ?>
            <div>Error: <font color="red"><?php echo($getToken_error); ?></font></div>
        <?php } ?>
        <?php if (isset($getToken_text)) { ?>
            <div>Info: <font color="green"><?php echo($getToken_text); ?></font></div>
        <?php } ?>
    </article>

    <article class="for">
        <header><h3>isValidToken</h3></header>

        <form method="post" target="_self" action="apifunctions.php">
            <table>
                <tr>
                    <th>Token</th>
                    <td><input id="token" name="token" type="text"
                               value="<?php echo(isset($_POST['token']) ? $_POST['token'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td><input id="domain" name="domain" type="text" value="<?php echo($domain); ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" id="submit_isValidToken" name="submit_isValidToken"
                                           value="isValidToken"/></td>
                </tr>
            </table>
        </form>

        <?php if (isset($isValidToken_error)) { ?>
            <div>Error: <font color="red"><?php echo($isValidToken_error); ?></font></div>
        <?php } ?>
        <?php if (isset($isValidToken_text)) { ?>
            <div>Info: <font color="green"><?php echo($isValidToken_text); ?></font></div>
        <?php } ?>
    </article>

    <article class="for">
        <header><h3>releaseToken</h3></header>

        <form method="post" target="_self" action="apifunctions.php">
            <table>
                <tr>
                    <th>Token</th>
                    <td><input id="token" name="token" type="text"
                               value="<?php echo(isset($_POST['token']) ? $_POST['token'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td><input id="domain" name="domain" type="text" value="<?php echo($domain); ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" id="submit_releaseToken" name="submit_releaseToken"
                                           value="releaseToken"/></td>
                </tr>
            </table>
        </form>

        <?php if (isset($releaseToken_error)) { ?>
            <div>Error: <font color="red"><?php echo($releaseToken_error); ?></font></div>
        <?php } ?>
        <?php if (isset($releaseToken_text)) { ?>
            <div>Info: <font color="green"><?php echo($releaseToken_text); ?></font></div>
        <?php } ?>
    </article>
</section>

<section>
    <header><h2>Authorization API</h2></header>

    <article class="for" style="height: auto;">
        <header><h3>getPermissions</h3></header>
        <form method="post" target="_self" action="apifunctions.php">
            <table>
                <tr>
                    <th>Path</th>
                    <td><input id="path" name="path" type="text"
                               value="<?php echo(isset($_POST['path']) ? $_POST['path'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Token</th>
                    <td><input id="token" name="token" type="text"
                               value="<?php echo(isset($_POST['token']) ? $_POST['token'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td><input id="domain" name="domain" type="text" value="<?php echo($domain); ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" id="submit_getPermissions" name="submit_getPermissions"
                                           value="getPermissions"/></td>
                </tr>
            </table>
        </form>

        <?php if (isset($getPermissions_error)) { ?>
            <div>Error: <font color="red"><?php echo($getPermissions_error); ?></font></div>
        <?php } ?>
        <?php if (isset($getPermissions_text)) { ?>
            <div>Info: <font color="green">
                    <pre><?php var_dump($getPermissions_text); ?></pre>
                </font></div>
        <?php } ?>

    </article>

</section>

<section>
    <header><h2>Attributes API</h2></header>

    <article class="for" style="height: auto;">
        <header><h3>getAttributes</h3></header>
        <form method="post" target="_self" action="apifunctions.php">
            <table>
                <tr>
                    <th>Path</th>
                    <td><input id="path" name="path" type="text"
                               value="<?php echo(isset($_POST['path']) ? $_POST['path'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Token</th>
                    <td><input id="token" name="token" type="text"
                               value="<?php echo(isset($_POST['token']) ? $_POST['token'] : ""); ?>"/></td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td><input id="domain" name="domain" type="text" value="<?php echo($domain); ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" id="submit_getAttributes" name="submit_getAttributes"
                                           value="getAttributes"/></td>
                </tr>
            </table>
        </form>

        <?php if (isset($getAttributes_error)) { ?>
            <div>Error: <font color="red"><?php echo($getAttributes_error); ?></font></div>
        <?php } ?>
        <?php if (isset($getAttributes_text)) { ?>
            <div>Info: <font color="green">
                    <pre><?php var_dump($getAttributes_text); ?></pre>
                </font></div>
        <?php } ?>

    </article>

    <article class="for">
        <header><h3>getAttributes for Domain</h3></header>
        <form method="post" target="_self" action="apifunctions.php">
            <table>
                <tr>
                    <th>Path</th>
                    <td></td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td><input id="domain" name="domain" type="text" value="<?php echo($domain); ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" id="submit_getDomainAttributes"
                                           name="submit_getDomainAttributes" value="getDomainAttributes"/></td>
                </tr>
            </table>
        </form>

        <?php if (isset($getDomainAttributes_error)) { ?>
            <div>Error: <font color="red"><?php echo($getDomainAttributes_error); ?></font></div>
        <?php } ?>
        <?php if (isset($getDomainAttributes_text)) { ?>
            <div>Info: <font color="green">
                    <pre><?php var_dump($getDomainAttributes_text); ?></pre>
                </font></div>
        <?php } ?>

    </article>

</section>

</body>
</html>
