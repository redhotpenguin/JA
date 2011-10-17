<?php

 /**
 * INBOX Highrise PHP Library
 *
 * Contact Data
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: ContactData.php 1000 2010-02-16 00:55:00Z marcan $
 */

class inbox_highrise_ContactData extends inbox_highrise_Object {
	/**
	 * snail addresses
	 * @var inbox_highrise_Address[]
	 */
	public $addresses = array();
	/**
	 * Phone numbers
	 * @var inbox_highrise_PhoneNumber[]
	 */
	public $phoneNumbers = array();
	/**
	 * e-mail addresses
	 * @var inbox_highrise_EmailAddress[]
	 */
	public $emails = array();
	/**
	 * web addresses
	 * @var inbox_highrise_WebAddress[]
	 */
	public $websites = array();


	public function toXml() {
		$body = "";
		if ( $this->addresses || $this->phoneNumbers || $this->emails || $this->websites ) {
			$body = "<contact-data>\r\n";
			if ( $this->addresses )		$body .= $this->xmlArray( "addresses",			$this->addresses );
			if ( $this->websites ) 		$body .= $this->xmlArray( "web-addresses",		$this->websites );
			if ( $this->emails ) 		$body .= $this->xmlArray( "email-addresses",	$this->emails );
			if ( $this->phoneNumbers ) 	$body .= $this->xmlArray( "phone-numbers",		$this->phoneNumbers );
			$body .= "\r\n</contact-data>\r\n";
		}
		return $body;
	}

	public function addComponent( $collection, $object, $location = "Work" ) {
		$object->location = $location;
		$coll =& $this->$collection;
		$coll[ $location ] = $object;
	}

}
?>