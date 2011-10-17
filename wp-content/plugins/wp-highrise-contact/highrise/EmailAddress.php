<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Email Address
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: EmailAddress.php 1000 2010-02-16 00:55:00Z marcan $
 */

class inbox_highrise_EmailAddress extends inbox_highrise_LocatedObject {
	/**
	 * Email address
	 * @var string
	 */
	public $address;

	public function __construct( $id = null, $address = "" ) {
		parent::__construct( $id );
		if ( $address ) $this->address = $address;
	}
	public function __toString() {
		return $this->address;
	}
	public function toXml() {
		$id = $this->id ? "\t<id type=\"integer\">$this->id</id>\r\n" : "";
		return "<email-address>\r\n$id\t<address>$this->address</address>\r\n\t<location>$this->location</location>\r\n</email-address>";
	}
}
?>