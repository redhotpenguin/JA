<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Phone number
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: PhoneNumber.php 1000 2010-02-16 00:55:00Z marcan $
 */

class inbox_highrise_PhoneNumber extends inbox_highrise_LocatedObject {
	/**
	 * Phone number
	 * @var string
	 */
	public $number;

	public function __construct( $id = null, $number = "" ) {
		parent::__construct( $id );
		if ( $number ) $this->number = $number;
	}
	public function __toString() {
		return $this->number;
	}
	public function toXml() {
		$id = $this->id ? "\t<id type=\"integer\">$this->id</id>\r\n" : "";
		return "<phone-number>\r\n$id\t<number>$this->number</number>\r\n\t<location>$this->location</location>\r\n</phone-number>";
	}
}
?>