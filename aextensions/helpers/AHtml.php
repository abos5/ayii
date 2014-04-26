<?php
class AHtml extends XHtml
{
	public static function buttonLikeLink($label,$href='', array $htmlOptions = array())
	{
		if(isset($htmlOptions['class']))
			$htmlOptions['class'] .= ' linkButton';
		else
			$htmlOptions['class'] = ' linkButton';
		return self::link($label,$href,$htmlOptions);
	}
	public static function littleButtonLikeLink($label,$href='', array $htmlOptions = array())
	{
		if(isset($htmlOptions['class']))
			$htmlOptions['class'] .= ' littleButton linkButton';
		else
			$htmlOptions['class'] = ' littleButton linkButton';
		return self::link($label,$href,$htmlOptions);
	}
}