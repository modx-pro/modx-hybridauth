<?php
class haUser extends modUser {

	/**
	 * Determines if the provided password matches the hashed password stored for the user.
	 *
	 * @param string $password The password to determine if it matches.
	 * @param array $options Optional settings for the hashing process.
	 * @return boolean True if the provided password matches the stored password for the user.
	 */
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
}