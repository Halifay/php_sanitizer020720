<?php

class FieldsManager
{
    private $structure;
    private $values;

    public function create_structure($structure)
    {
        if(!is_array($structure))
        {
            throw new InvalidArgumentException("Structure must be array.");
        }
        $this->structure = $structure;
    }

    public function __construct($structure, $values)
    {
        $this->create_structure($structure);
        $this->values = $this->create_array($structure, json_decode($values, true, 10));

    }

    public function get_sanitized_object()
    {
        return $this->values;
    }

    private function create_array($structure, $values)
    {
        if($values == null)
        {
            throw new InvalidArgumentException("Invalid json.");
        }
        $current_values=[];
        foreach ($structure as $key=>$type)
        {
            if(!array_key_exists($key, $values))
            {
                throw new UnexpectedValueException("Json doesn't have declared in the structure ".$key." key.");
            }
            if($this->is_array($type))
            {
                $new_type = substr($type, 0, -2);
                // print $new_type."\n";
                $current_values[$key] = [];// new $type($values[$key]);
                foreach($values[$key] as $elem)
                {
                    array_push($current_values[$key], (new $new_type($elem))->get_value());
                }
                continue;
            }
            //if this is dictionary
            if($this->is_dict($type))
            {
                $current_values[$key] = $this->create_array($type, $values[$key]);
                continue;
            }

            $current_values[$key] = (new $type($values[$key]))->get_value();
            // print $current_values[$key]."\n";
        }
        return $current_values;
    }

    private function is_array($type)
    {
        if(!is_string($type))
            return false;
        return substr($type, -1) == ']';
        // return is_array($type);
        // return is_array(new $type());
    }

    private function is_dict($type)
    {
        return !is_string($type);
    }

    public function dump_values()
    {
        var_dump($this->values);
    }

}

class FloatField
{
    private $float_field;
    public function __construct($float_field)
    {
        $this->float_field = $this->float_from_str($float_field);
    }

    public function get_float()
    {
        return $this->float_field;
    }

    private function float_from_str($float_field)
    {
        if($this->is_float($float_field))
        {
            return floatval($float_field);
        }
        throw new UnexpectedValueException("float_field must be convertible to float type.");
    }

    private function is_float($float_field)
    {
        return (strval(floatval($float_field))==$float_field);
    }

    public function get_value()
    {
        return $this->float_field;
    }
}

class IntegerField
{
    private $integer_field;
    public function __construct($integer_field)
    {
        $this->integer_field = $this->int_from_str($integer_field);
    }

    public function get_value()
    {
        return $this->integer_field;
    }

    private function int_from_str($integer_field)
    {
        if($this->is_integer($integer_field))
        {
            return intval($integer_field);
        }
        throw new UnexpectedValueException("The argument must be convertible to integer_field type.");
    }

    private function is_integer($integer_field)
    {
        return (strval(intval($integer_field))==$integer_field);
    }

}


class StringField
{
    private $string_field;
    public function __construct($string_field)
    {
        $this->string_field = strval($string_field);
    }

    public function get_value()
    {
        return $this->string_field;
    }
}


class RussianPhoneNumber
{
    private $number;
    public function __construct($number)
    {
        $var = $this->validate_russian_phone_number($number);
        if($var == true)
        {
            $this->number = $this->get_pure_number($number);
            return;
        }
        throw new UnexpectedValueException("Incorrect phone number.");
    }

    public function get_value()
    {
        return $this->number;
    }

    private function validate_russian_phone_number($number)
    {
        $reg = '#^(8|7|\+7)(( |-)?)(\([0-9]{3}\)|[0-9]{3})(( |-)?)([0-9]{3})(( |-)?)([0-9]{2})(( |-)?)([0-9]{2})$#';
        preg_match($reg, $number, $m);
        if(empty($m))
            return 0;
        return 1;
    }

    private function get_pure_number($number)
    {
        return preg_replace('#[^0-9]#', '', $number);
    }

}

?>



