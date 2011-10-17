<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Tag
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: Tag.php 1004 2010-02-16 01:55:39Z marcan $
 */

class inbox_highrise_Tag extends inbox_highrise_Object {
	/**
	 * Tag name
	 * @var string
	 */
	public $name;

	public function __construct( $id = null, $name = "" ) {
		parent::__construct( $id );
		if ( $name ) $this->name = $name;
	}
	public function __toString() {
		return $this->name;
	}
	public function toXml() {
		return "<tag>\r\n"
			. $this->xmlProperty( "id",				$this->id )
			. $this->xmlProperty( "name",			$this->name )
			. "</tag>";
	}

	static function listEntities( $connection ) {
		$str = $connection->execute( "GET", $connection->defaultUrl( "inbox_highrise_Tag" ) . ".xml" );
		$xml = simplexml_load_string( $str );

		$tags = array();
		foreach ( $xml->tag as $tag ) {
			$tags[(int)$tag->id] = (string)$tag->name;
  		}
  		return $tags;
	}
}
?>