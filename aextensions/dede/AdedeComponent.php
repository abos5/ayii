<?php

class AdedeComponent extends CApplicationComponent
{

	public $attlist;
	public $ctag;
	public $refObj;

	public $global;

	public function init()
	{
		parent::init();
	}
	public function getCfg()
	{

	}
	public function setCfg()
	{

	}
	/**
	 * @todo don't think quote use is cool here;
	 */
	public function setUp($ctag,$refObj)
	{
		$this->ctag = $ctag;
		$this->refObj = $refObj;
		FillAttsDefault($this->ctag->CAttribute->Items,$this->attlist);
		$_GET['r'] = $this->ctag->CAttribute->Items['r'];
	}
	public function unsetUp()
	{
		$this->ctag = '';
		$this->refObj = '';
		// unset($this->ctag);
		// unset($this->refObj);
	}

	public function getVar($key)
	{
		return $GLOBALS[$key];
	}

	public function getConfig($key)
	{
		$key = 'cfg_'.$key;
		$var = $GLOBALS[$key];
		if(!$var)
			$var = Dedeconfig::model()->findByAttributes(array('varname'=>$key))->value;
		return $var;
	}
	public function getTopId()
	{

	}
	/**
	 */
	public function getParentTypeId($typeId)
	{
		$types = $GLOBALS['cfg_Cs'];
		return $types[$typeId][0];
	}

	public function getPaginationHref($mkpage=false,$tid=false,$params=array())
	{

		if($tid===false)
			$tid = $this->getVar('tid');
		if($mkpage===false)
			$mkpage = $this->getVar('mkpage');

		$lv = $this->getVar('lv');
		$indexUrl = $lv->TypeLink->indexUrl ;
		$typeDir = $lv->TypeLink->TypeInfos['typedir'];
		$nameRule2 = $lv->TypeLink->TypeInfos['namerule2'];
		
		$typeDir = strtr($typeDir, array('{cmspath}'=>$indexUrl));


		if($mkpage===0)
			$mkpage = 1;
		if($mkpage > $lv->TotalPage)
			$mkpage = $lv->TotalPage;


		return strtr($nameRule2, array(
			'{typedir}'=>$typeDir,
			'{tid}'=>$tid,
			'{page}'=>$mkpage,
		));

	}

	public function getUploadDir($type)
	{
		// $cmspath = $this->getConfig('cmspath');
		$tails = array(
			'products'=>'../../uploads/products',
		);
		return getcwd().'/'.$tails[$type];
	}
	/**
	 */
	public function getFileNameForUpload($type)
	{
		$arr = array(
			'products'=>microtime(),
		);
		return preg_replace('#[\.\s]#', '', $arr[$type]);
	}
	public function getAllArctypeOptions()
	{
		require_once(DEDEINC."/typelink.class.php");
		$tl = new TypeLink($cid);
		return  $tl->GetOptionArray(0, 0, 0);
	}
}




	