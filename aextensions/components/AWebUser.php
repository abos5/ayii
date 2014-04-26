<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class AWebUser extends CWebUser
{
	
	public $info;
	/**
	 * Changes the current user with the specified identity information.
	 * This method is called by {@link login} and {@link restoreFromCookie}
	 * when the current user needs to be populated with the corresponding
	 * identity information. Derived classes may override this method
	 * by retrieving additional user-related information. Make sure the
	 * parent implementation is called first.
	 * @param mixed $id a unique identifier for the user
	 * @param string $name the display name for the user
	 * @param array $states identity states
	 */
	protected function changeIdentity($id,$name,$states)
	{
		Yii::app()->getSession()->regenerateID(true);
		$this->setId($id);
		// the only part that changes;
		Yii::import('hra.models.Personnel');
		$this->info = Personnel::model()->findByPk($id);
		// $this->setName($this->info->personnel_number);
		$this->setName($this->info->PersonnelID);
		$this->loadIdentityStates($states);
	}
}