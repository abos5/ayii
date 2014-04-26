<?php

class Afile
{
	public static function ext($fileName,$wantDot=false)
	{
		$start = $wantDot ? strpos($fileName, '.') :strpos($fileName, '.')+1;
		return substr($fileName, $start );
	}
	
}