#!/usr/bin/php
<?php
require_once 'CredioClient.php';

function authenticate($user, $pass)
{
    $credio = Credio\Client::getInstance();

    try {
        $credio->authenticate($user, $pass);
        echo("authenticate - OK\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function getToken($user, $pass)
{
    $credio = Credio\Client::getInstance();

    try {
        $t = $credio->getToken($user, $pass);
        echo("token: " . $t . "\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function releaseToken($token_str)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $t = $credio->releaseToken($t);
        echo("release token: " . $token_str . " - OK\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function isValidToken($token_str)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $t = $credio->isValidToken($t);
        echo("token: " . $token_str . " - OK\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function getAttributes($path, $token_str = NULL)
{
    $credio = Credio\Client::getInstance();
    $t = $token_str ? new Credio\Token($token_str) : NULL;

    try {
        $attr = $credio->getAttributes($path, $t);
        print_r($attr);
        echo("\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function getPermissions($path, $token_str = NULL)
{
    $credio = Credio\Client::getInstance();
    $t = $token_str ? new Credio\Token($token_str) : NULL;

    try {
        $perms = $credio->getPermissions($path, $t);
        print_r($perms);
        echo("\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function userSearch($path, $token_str, $opt = NULL)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $users = $credio->userSearch($path, $t, $opt);
        print_r($users);
        echo("\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function groupSearch($path, $token_str, $opt = NULL)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $groups = $credio->groupSearch($path, $t, $opt);
        print_r($groups);
        echo("\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function attrInsert($target, $targetType, $attribute, $token_str)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $attrId = $credio->attrInsert($target, $targetType, $attribute, $t);
        echo("attribute created with ID " . $attrId . "\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function attrRemove($target, $targetType, $attribute, $token_str)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $credio->attrRemove($target, $targetType, $attribute, $t);
        echo("attribute removed.\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function userInsert($username, $password, $token_str)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $u = $credio->userInsert($username, $password, $t);
        echo("user inserted with ID " . $u->id . "\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function userRemove($user, $token_str)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $credio->userRemove($user, $t);
        echo("user removed.\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function groupInsert($group, $token_str)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $g = $credio->groupInsert($group, $t);
        echo("group inserted with ID " . $g->id . "\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function groupRemove($group, $token_str)
{
    $credio = Credio\Client::getInstance();
    $t = new Credio\Token($token_str);

    try {
        $credio->groupRemove($group, $t);
        echo("group removed.\n");
    } catch (Credio\Exception $ex) {
        echo($ex->getMessage() . "\n");
        exit(1);
    }
}

function usage()
{
    global $argv;
    echo($argv[0] . " <function> [<param0> <param1> ... <paramN>]\n");
    echo("available functions:\n");
    echo("\tauthenticate\t\tusername password\n");
    echo("\tgetToken\t\tusername password\n");
    echo("\tisValidToken\t\ttokenString\n");
    echo("\treleaseToken\t\ttokenString\n");
    echo("\tuserSearch\t\tpattern tokenString options\n");
    echo("\tuserInsert\t\tusername password tokenString\n");
    echo("\tuserRemove\t\tuserId|userName tokenString\n");
    echo("\tgroupSearch\t\tpattern tokenString options\n");
    echo("\tgroupInsert\t\tgroupName tokenString\n");
    echo("\tgroupRemove\t\tgroupId|groupName tokenString\n");
    echo("\tattrInsert\t\ttarget targetType attrName attrValue tokenString\n");
    echo("\tattrRemove\t\ttarget targetType attrId|attrName tokenString\n");
    echo("\tgetPermissions\t\tpath tokenString\n");
    echo("\tgetAttributes\t\tpath tokenString\n");
    exit(1);
}

if ($argc < 2) usage();

$cmd = $argv[1];

switch ($cmd) {
    case "authenticate" :
        if ($argc != 4) usage();
        authenticate($argv[2], $argv[3]);
        break;
    case "isValidToken" :
        if ($argc != 3) usage();
        isValidToken($argv[2]);
        break;
    case "getToken" :
        if ($argc != 4) usage();
        getToken($argv[2], $argv[3]);
        break;
    case "releaseToken" :
        if ($argc != 3) usage();
        releaseToken($argv[2]);
        break;
    case "getAttributes" :
        if ($argc != 4) usage();
        $p = isset($argv[2]) ? $argv[2] : NULL;
        $t = isset($argv[3]) ? $argv[3] : NULL;
        getAttributes($p, $t);
        break;
    case "getPermissions" :
        if ($argc != 4) usage();
        $p = isset($argv[2]) ? $argv[2] : NULL;
        $t = isset($argv[3]) ? $argv[3] : NULL;
        getPermissions($p, $t);
        break;
    case "userSearch" :
        if ($argc < 4) usage();
        $p = isset($argv[2]) ? $argv[2] : NULL;
        $t = isset($argv[3]) ? $argv[3] : NULL;
        $o = isset($argv[4]) ? $argv[4] : NULL;
        userSearch($p, $t, $o);
        break;
    case "userInsert" :
        if ($argc != 5) usage();
        $u = isset($argv[2]) ? $argv[2] : NULL;
        $p = isset($argv[3]) ? $argv[3] : NULL;
        $t = isset($argv[4]) ? $argv[4] : NULL;
        userInsert($u, $p, $t);
        break;
    case "userRemove" :
        if ($argc != 4) usage();
        $i = isset($argv[2]) ? $argv[2] : NULL;
        $t = isset($argv[3]) ? $argv[3] : NULL;
        $u = new \Credio\User();
        if (is_numeric($i))
            $u->id = $i;
        else
            $u->uname = $i;
        userRemove($u, $t);
        break;
    case "groupSearch" :
        if ($argc < 4) usage();
        $p = isset($argv[2]) ? $argv[2] : NULL;
        $t = isset($argv[3]) ? $argv[3] : NULL;
        $o = isset($argv[4]) ? $argv[4] : NULL;
        groupSearch($p, $t, $o);
        break;
    case "groupInsert" :
        if ($argc != 4) usage();
        $g = isset($argv[2]) ? $argv[2] : NULL;
        $t = isset($argv[3]) ? $argv[3] : NULL;
        groupInsert($g, $t);
        break;
    case "groupRemove" :
        if ($argc != 4) usage();
        $i = isset($argv[2]) ? $argv[2] : NULL;
        $t = isset($argv[3]) ? $argv[3] : NULL;
        $g = new \Credio\Group();
        if (is_numeric($i))
            $g->id = $i;
        else
            $g->name = $i;
        groupRemove($g, $t);
        break;
    case "attrInsert" :
        if ($argc != 7) usage();
        $t = isset($argv[2]) ? $argv[2] : NULL;
        $tt = isset($argv[3]) ? $argv[3] : NULL;
        $ak = isset($argv[4]) ? $argv[4] : NULL;
        $av = isset($argv[5]) ? $argv[5] : NULL;
        $tok = isset($argv[6]) ? $argv[6] : NULL;
        $a = new Credio\Attribute();
        $a->key = $ak;
        $a->value = $av;
        $a->type = Credio\AttributeType::STRING;
        attrInsert($t, $tt, $a, $tok);
        break;
    case "attrRemove" :
        if ($argc != 6) usage();
        $t = isset($argv[2]) ? $argv[2] : NULL;
        $tt = isset($argv[3]) ? $argv[3] : NULL;
        $ak = isset($argv[4]) ? $argv[4] : NULL;
        $tok = isset($argv[5]) ? $argv[5] : NULL;
        $a = new Credio\Attribute();
        if (is_numeric($ak))
            $a->id = $ak;
        else
            $a->key = $ak;
        attrRemove($t, $tt, $a, $tok);
        break;
    default:
        usage();
        break;
}