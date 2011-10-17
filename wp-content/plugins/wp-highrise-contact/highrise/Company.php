<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Company
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: Company.php 1000 2010-02-16 00:55:00Z marcan $
 */

class inbox_highrise_Company extends inbox_highrise_Object {
	/**
	 * Company name
	 * @var string
	 */
	public $name;
	/**
	 * Background text
	 * @var string
	 */
	public $background = "";
	/**
	 * Contact data
	 * @var inbox_highrise_ContactData
	 */
	public $contact;

	public function __construct( $id = null, $name = "" ) {
		parent::__construct( $id );
		if ( $name ) $this->name = $name;
		$this->contact = new inbox_highrise_ContactData();
	}

	public function __toString() {
		return $this->name;
	}
	public function toXml() {
		return "<company>\r\n"
			. $this->xmlProperty( "id",				$this->id )
			. $this->xmlProperty( "name",			$this->name )
			. $this->xmlProperty( "background",		$this->background )
			. $this->xmlProperty( "contact-data",	$this->contact )
			. "</company>";
	}

}
?>