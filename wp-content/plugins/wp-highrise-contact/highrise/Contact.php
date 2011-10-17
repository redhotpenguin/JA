<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Contact
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: Contact.php 1000 2010-02-16 00:55:00Z marcan $
 */

class inbox_highrise_Contact extends inbox_highrise_Object {
	/**
	 * First name
	 * @var string
	 */
	public $firstName = "";
	/**
	 * Last name
	 * @var string
	 */
	public $lastName = "";
	/**
	 * Company name
	 * @var inbox_highrise_Company
	 */
	public $company = "";
	/**
	 * Title
	 * @var string
	 */
	public $title = "";
	/**
	 * Background
	 * @var string
	 */
	public $background = "";
	/**
	 * Contact data
	 * @var inbox_highrise_ContactData
	 */
	public $contact;

	public function __construct() {
		parent::__construct();
		$this->contact = new inbox_highrise_ContactData();
	}

	/**
	 * Post the current object to the specified server
	 * @param inbox_highrise_CurlConnection $server
	 * @return mixed
	 */
	public function toXml() {
		$body = "<person>\r\n"
			. $this->xmlProperty( "id",				$this->id )
			. $this->xmlProperty( "first-name",		$this->firstName )
			. $this->xmlProperty( "last-name",		$this->lastName )
			. $this->xmlProperty( "title",			$this->title )
			. $this->xmlProperty( "background",		$this->background );
		if ( $this->company ) {
			if ( $this->company instanceof inbox_highrise_Company ) {
				if ( $this->company->id)			$body .= "\t<company-id type=\"integer\">{$this->company->id}</company-id>\r\n";
				else								$body .= "\t<company-name>{$this->company->name}</company-name>\r\n";
			} elseif ( is_int( $this->company ) )	$body .= "\t<company-id type=\"integer\">$this->company</company-id>\r\n";
		}
		$body .= $this->contact->toXml() . "\r\n</person>";
		return $body;
	}

}
?>