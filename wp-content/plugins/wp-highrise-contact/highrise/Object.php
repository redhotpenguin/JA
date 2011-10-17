<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Highrise Object
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: Object.php 1000 2010-02-16 00:55:00Z marcan $
 */


abstract class inbox_highrise_Object {
	/**
	 * Object ID
	 * @var int
	 */
	public $id;

	/**
	 * Public constructor
	 */
	public function __construct( $id = null ) {
		if ( $id )	$this->id = (int)$id;
	}

	/**
	 * Convert this object to Xml
	 * @return string
	 */
	abstract public function toXml();

	/**
	 * Render the specified property as an xml element
	 * @param string $tag
	 * @param mixed $value
	 * @return string
	 */
	public function xmlProperty( $tag, $value, $type = null ) {
		if ( $tag == "id" && !$value ) {
			return "";
		} else if ( $value instanceof inbox_highrise_Object ) {
			return "\t" . $value->toXml() . "\r\n";
		} else if ( isset( $type ) ) {
			return "\t<$tag type=\"$type\">$value</$tag>\r\n";
		} else if ( is_int( $value ) ) {
			return "\t<$tag type=\"integer\">$value</$tag>\r\n";
		} else if ( !isset( $value ) || !$value ) {
			return "\t<$tag nil=\"true\" />\r\n";
		} else {
			return "\t<$tag>$value</$tag>\r\n";
		}
	}
	/**
	 * Convert the specified collection to xml
	 * @param string $tag
	 * @param array $array
	 * @return string
	 */
	public function xmlArray( $tag, $array ) {
		$items = array();
		foreach ( $array as $item ) $items[] = $item->toXml();
		return "<$tag>\r\n\t" . implode( "\r\n\t", $items ) . "\r\n</$tag>\r\n";
	}


}

abstract class inbox_highrise_LocatedObject extends inbox_highrise_Object {
	/**
	 * Location
	 * @var string
	 */
	public $location = "Work";

}
?>