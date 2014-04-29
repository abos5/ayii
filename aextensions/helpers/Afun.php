<?php
class Afun
{

	/**
	 * convert array into object recursively;
	 * makes this array an property of parent value
	 * if one array key is numeric 
	 */
  public static function array2Object( &$array )
  {
  	if(!is_array($array)) return $array;
  	
  	array_walk($array, 'self::array2Object');

    if(!self::areKeysNumeric($array)) $array = (object) $array ;
    return $array;
  }
	public static function areKeysNumeric( $array )
	{
		foreach ($array as $key => $value)
			if(is_numeric($key)) return true;
	}
	public static function checkDate($time)
	{
		if(!is_numeric($time) && is_string($time))
			$time = strtotime($time);

		$year = date('Y',$time);
		$month = date('m',$time);
		$day = date('d',$time);

		return checkDate($month,$day,$year);
	}
	
	public static function amicrotime()
	{
		$time = explode(' ', microtime());
		return (float) $time[1] + (float) $time[0];
	}
}
