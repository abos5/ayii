<?php 
/**
 * 
 * Enter description here ...
 * @author alva.wu@qq.com
 *
 */
class Aclient
{
	private $options = array();
	private $scriptName;
	private $startSecond;
	private $silent;
	private $startMicrosecond;
	
	public function __construct()
	{
		list( $this->startMicrosecond, $this->startSecond )  = explode( ' ', microtime() );
		global $argv;
		$this->scriptName = array_shift($argv);
		foreach($argv as $arg)
		{
			if(strpos($arg, '--') === 0)
			{
				list($argName ,$value) = explode('=', $arg) ;
				$argName = substr($argName, 2);
				$this->options[$argName] = $value;
			}
		}
		$this->silent = false;
		if( $this->getOption('silent') && $this->getOption('silent')  == 1)
		{
			$this->silent = true;
		}
	}
	
	public function getOption($key, $default = false)
	{
		if( isset($this->options[$key]) )
		{
			return $this->options[$key];
		}
		else
		{
			return $default;
		}
	}

	public function setOption($key, $value)
	{
		$this->options[$key] = $value;
	}
	
	public function getStartTime()
	{
		return $this->startSecond;
	}

	public function resetStartTime()
	{
		list( $this->startMicrosecond, $this->startSecond )  = explode( ' ', microtime() );
	}
	
	public function getTimeUsed()
	{
		list( $curMicrosecond, $curSecond ) = explode(' ', microtime());
		$timeUsed = '';
		$usedMicrosend 	= $curMicrosecond - $this->startMicrosecond;
		$usedSecond 	= $curSecond - $this->startSecond;
		if( $usedMicrosend < 0 )
		{
			$usedSecond = $usedSecond - 1;
			$usedMicrosend = 1 + $usedMicrosend;
		}
		$usedMicrosend = (string) $usedMicrosend;
		$usedMicrosend = substr($usedMicrosend, 2);
		return $usedSecond . '.' . $usedMicrosend;
	}

	public function printLine( $line )
	{
		if( $this->silent ) return ;
		echo $line . PHP_EOL;
	}

	public static function combineParams( $params )
	{
		$str = array();
		foreach( $params as $key => $param )
		{
			$str[] = '--' . $key . '=' . $param;
		}
		return implode(' ', $str);
	}
}