<?php
require_once("sanitizer.php");

function test_float()
{
    $result = true;

    return $result;
}

function test_integer()
{
    $result = true;

    return $result;
}

function test_string()
{
    $result = true;

    return $result;
}

function test_russian_phone_number()
{
    $result = true;

    return $result;
}

print test_float();
print test_integer();
print test_string();
print test_russian_phone_number();
?>