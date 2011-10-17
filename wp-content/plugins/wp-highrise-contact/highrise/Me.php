<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Me
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: Task.php 1000 2010-02-16 00:55:00Z marcan $
 */

class inbox_highrise_Me extends inbox_highrise_Object {
	/**
	 * Name
	 * @var string
	 */
	public $name = "";

	public function __construct( $id = null, $name = "" ) {
		parent::__construct( $id );
		if ( $name ) $this->name = $name;
	}
	public function __toString() {
		return $this->name;
	}

	public function toXml() {
		return "";
	}

	static function load( $connection ) {
		$str = $connection->execute( "GET", $connection->defaultUrl( "inbox_highrise_Me" ) . ".xml" );
		$xml = simplexml_load_string( $str );
  		return $xml;
	}
}
?>