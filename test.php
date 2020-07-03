<?php
require_once("sanitizer.php");
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    public function testProperFloat()
    {
        $flt = new FloatField("3.14");
        $this->assertEquals(3.14, $flt->get_value());
    }

    public function testFloatFromInt()
    {
        $flt = new FloatField("314");
        $this->assertEquals(314, $flt->get_value());
    }

    public function testLittersInFloatException()
    {
        $this->expectException(UnexpectedValueException::class);
        $flt = new FloatField("3.1ab592");
    }

    public function testTooBigFloatException()
    {
        $this->expectException(UnexpectedValueException::class);
        $flt = new FloatField("123456789012345678901234567890.123456789");
    }


    public function testProperInteger()
    {
        $intf = new IntegerField("123456");
        $this->assertEquals(123456, $intf->get_value());
    }

    public function testFloatInsteadOfIntegerException()
    {
        $this->expectException(UnexpectedValueException::class);
        $intf = new IntegerField("3.141592");
    }
    public function testLitersInIntegerException()
    {
        $this->expectException(UnexpectedValueException::class);
        $intf = new IntegerField("asd123");
    }

    public function testTooBigIntegerException()
    {
        $this->expectException(UnexpectedValueException::class);
        $intf = new IntegerField("123456789012345678901234567890");
    }

    public function testProperString()
    {
        $arg = "wqersadfersdfa";
        $str = new StringField($arg);
        $this->assertEquals($arg, $str->get_value());
    }

    public function testReadyProperNumber()
    {
        $tel = new RussianPhoneNumber("88005553535");
        $this->assertEquals("88005553535", $tel->get_value());
    }

    public function testProperNumberWithSpaces()
    {
        $tel = new RussianPhoneNumber("8 800 555 35 35");
        $this->assertEquals("88005553535", $tel->get_value());
    }

    public function testProperNumberWithDashes()
    {
        $tel = new RussianPhoneNumber("8-800-555-35-35");
        $this->assertEquals("88005553535", $tel->get_value());
    }

    public function testProperNumberWithParenthesis()
    {
        $tel = new RussianPhoneNumber("8(800)5553535");
        $this->assertEquals("88005553535", $tel->get_value());
    }

    public function testProperNumberWithEverything()
    {
        $tel = new RussianPhoneNumber("+7 (800) 555-35-35");
        $this->assertEquals("78005553535", $tel->get_value());
    }

    public function testBadFirstDigitException()
    {
        $this->expectException(UnexpectedValueException::class);
        $tel = new RussianPhoneNumber("98005553535");
    }

    public function testTooManyDigitsException()
    {
        $this->expectException(UnexpectedValueException::class);
        $tel = new RussianPhoneNumber("788005553535");
    }

    public function testNotEnoughDigitsException()
    {
        $this->expectException(UnexpectedValueException::class);
        $tel = new RussianPhoneNumber("8805553535");
    }

    public function testBadCharactersException()
    {
        $this->expectException(UnexpectedValueException::class);
        $tel = new RussianPhoneNumber("88055S3535");
    }

    public function testProperFieldManager()
    {
        $result = 'a:3:{s:3:"foo";i:123;s:3:"bar";s:3:"asd";s:3:"baz";s:11:"89502885623";}';
        $structure = ['foo'=>IntegerField::class, 'bar'=>StringField::class, 'baz'=>RussianPhoneNumber::class];
        $json = '{"foo": "123", "bar": "asd", "baz": "8 (950) 288-56-23"}';
        $fm = new FieldsManager($structure, $json);
        $this->assertEquals($result, serialize($fm->get_sanitized_object()));
    }

    public function testProperComplicatedFieldManager()
    {
        $result = 'a:4:{s:3:"foo";i:123;s:3:"bar";a:2:{s:4:"bar2";a:1:{s:4:"bar3";d:3.14;}s:5:"nobar";s:3:"asd";}s:3:"arr";a:4:{i:0;i:3;i:1;i:14;i:2;i:15;i:3;i:92;}s:3:"baz";s:11:"89502885623";}';
        $structure = ['foo'=>IntegerField::class, 'bar'=>['bar2'=>['bar3'=>FloatField::class], 'nobar'=>StringField::class], 'arr'=>IntegerField::class.'[]', 'baz'=>RussianPhoneNumber::class];
        $json = '{"foo": "123", "bar": {"bar2": {"bar3": "3.14"}, "nobar": "asd"}, "arr": ["3", "14", "15", "92"], "baz": "8 (950) 288-56-23"}';
        $fm = new FieldsManager($structure, $json);
        $this->assertEquals($result, serialize($fm->get_sanitized_object()));
    }

    public function testTooManyKeysInJson()
    {
        $result = 'a:3:{s:3:"foo";i:123;s:3:"bar";s:3:"asd";s:3:"baz";s:11:"89502885623";}';
        $structure = ['foo'=>IntegerField::class, 'bar'=>StringField::class, 'baz'=>RussianPhoneNumber::class];
        $json = '{"foo": "123", "bar": "asd", "baz": "8 (950) 288-56-23", "chto-to": "escho"}';
        $fm = new FieldsManager($structure, $json);
        $this->assertEquals($result, serialize($fm->get_sanitized_object()));
    }

    public function testNotEnoughKeysException()
    {
        $this->expectException(UnexpectedValueException::class);
        $structure = ['foo'=>IntegerField::class, 'bar'=>StringField::class, 'baz'=>RussianPhoneNumber::class];
        $json = '{"foo": "123", "baz": "8 (950) 288-56-23"}';
        $fm = new FieldsManager($structure, $json);
    }

    public function testInvalidJsonException()
    {
        $this->expectException(InvalidArgumentException::class);
        $structure = ['foo'=>IntegerField::class, 'bar'=>StringField::class, 'baz'=>RussianPhoneNumber::class];
        $json = '{"foo": "123", "baz": "8 (950) 288-56-23"';
        $fm = new FieldsManager($structure, $json);
    }


}

?>