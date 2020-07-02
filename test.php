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

function test_field_manager()
{
    $result = true;
    $structures = [['foo'=>'IntegerField', 'jopa'=>['foo2'=>'StringField', 'bar2'=>'FloatField'], 'bar'=>'StringField', 'qwe'=>'IntegerField[]']];
    $values = [['foo'=>'123', 'bar'=>'foobar', 'jopa'=>['foo2'=>"argh", 'bar2'=>"228.13"], 'qwe'=>[1, 2, 3, 4]]];
    $answers = ['a:4:{s:3:"foo";i:123;s:4:"jopa";a:2:{s:4:"foo2";s:4:"argh";s:4:"bar2";d:228.13;}s:3:"bar";s:6:"foobar";s:3:"qwe";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}'];
    for($i=0; $i<sizeof($values); $i++)
    {
        $manager = new FieldsManager($structures[$i], $values[$i]);
        if($answers[$i] != serialize($manager->get_sanitized_object()))
        {
            throw new UnexpectedValueException("Test number ".strval($i)." has failed.");
        }
    }

    return $result;
}

print test_float();
print test_integer();
print test_string();
print test_russian_phone_number();
print test_field_manager();
?>