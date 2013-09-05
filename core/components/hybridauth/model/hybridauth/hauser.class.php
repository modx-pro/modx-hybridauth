<?php
class haUser extends modUser {

	/** {inheritDoc} */
	public function passwordMatches($password, array $options = array()) {
		if (!empty($_SESSION['HA']['verified'])) {
			$match = true;
			unset($_SESSION['HA']['verified']);
		}
		else {
			$match = false;
			if ($this->xpdo->getService('hashing', 'hashing.modHashing')) {
				$options = array_merge(array('salt' => $this->get('salt')), $options);
				$hashedPassword = $this->xpdo->hashing->getHash('', $this->get('hash_class'))->hash($password, $options);
				$match = ($this->get('password') === $hashedPassword);
			}
		}
		return $match;
	}


	/** {inheritDoc} */
	public function get($k, $format = null, $formatTemplate= null) {
		if (is_string($k) && strtolower($k) == 'gravatar') {
			return 'http://gravatar.com/avatar/' . md5(strtolower($this->Profile->get('email')));
		}
		else {
			return parent::get($k, $format, $formatTemplate);
		}
	}


	/** {inheritDoc} */
	public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated= false) {
		$array = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);
		$array['gravatar'] = $this->get('gravatar');

		return $array;
	}
}