<?php
class AArray2Object 
{

  public static function array_to_obj($array)
  {
  	$con  = self::getContainer($array);
    foreach ($array as $key => $value)
    {
      if (is_array($value))
      {
	      $container = self::getContainer($array);
	      $rs = self::array_to_obj($value);
      }
      else
      {
        $rs = $value;
      }
      if(is_object($con))
      	$con->{$key} = $rs;
      if(is_array($con))
      	$con[] = $rs;
      
    }
  	return $con;
  }

	public static function convert($array)
	{
		return self::array_to_obj($array);
	}
	public static function getContainer($array)
	{
		if(self::areKeysNumberic($array))
			$object = array();
		else
			$object= new stdClass();
		return $object;
	}
	public static function areKeysNumeric( $array)
	{
		// var_dump($array);
		$keys = array_keys($array);
		foreach ($keys as $value) 
		{
			if(is_numeric($value))
				return true;
		}
		return false;
	}
	public static function areKeysNumberic($array)
	{
		return self::areKeysNumeric($array);
	}
}
