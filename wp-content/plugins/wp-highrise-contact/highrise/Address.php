<?php
 /**
 * INBOX Highrise PHP Library
 *
 * People Addresses
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: Address.php 1000 2010-02-16 00:55:00Z marcan $
 */

class inbox_highrise_Address extends inbox_highrise_LocatedObject {
	/**
	 * Street address
	 * @var string
	 */
	public $street;
	/**
	 * Zip code
	 * @var string
	 */
	public $zip;
	/**
	 * City
	 * @var string
	 */
	public $city;
	/**
	 * State
	 * @var string
	 */
	public $state;
	/**
	 * Country
	 * @var string
	 */
	public $country;

	public function __construct( $id = null, $street = "" ) {
		parent::__construct( $id );
		if ( $street ) $this->street = $street;
	}

	public function __toString() {
		return $this->street;
	}
	public function toXml() {
		return "<address>\r\n"
			. $this->xmlProperty( "id",			$this->id )
			. $this->xmlProperty( "street",		$this->street )
			. $this->xmlProperty( "zip",		$this->zip )
			. $this->xmlProperty( "city",		$this->city )
			. $this->xmlProperty( "state",		$this->state )
			. $this->xmlProperty( "country",	$this->country )
			. $this->xmlProperty( "location",	$this->location )
			. "</address>";
	}
}
?>