<?php

namespace ChildThemify;

use PHPUnit\Framework\Constraint\Constraint;
use Mockery;
use Exception;

class ExpectationsMetConstraint extends Constraint {

	private $_mockery_message;

	public function matches( $other ) {
		try {
			Mockery::getContainer()->mockery_verify();
		} catch ( Exception $e ) {
			$this->_mockery_message = $e->getMessage();

			return false;
		}

		return true;
	}

	/**
	 * Returns a string representation of the object.
	 *
	 * @return string
	 */
	public function toString() {
		return 'WP Mock expectations are met';
	}

	protected function additionalFailureDescription( $other ) {
		return str_replace( array( "\r", "\n" ), '', (string) $this->_mockery_message );
	}

	protected function failureDescription( $other ) {
		return $this->toString();
	}

}
