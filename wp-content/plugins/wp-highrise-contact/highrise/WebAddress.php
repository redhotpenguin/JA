<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Web address
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: WebAddress.php 1000 2010-02-16 00:55:00Z marcan $
 */

class inbox_highrise_WebAddress extends inbox_highrise_LocatedObject {
	/**
	 * URL
	 * @var string
	 */
	public $url;

	public function __construct( $id = null, $url = "" ) {
		parent::__construct( $id );
		if ( $url ) $this->url = $url;
	}
	public function __toString() {
		return $this->url;
	}
	public function toXml() {
		$id = $this->id ? "\t<id type=\"integer\">$this->id</id>\r\n" : "";
		return "<web-address>\r\n$id\t<url>$this->url</url>\r\n\t<location>$this->location</location>\r\n</web-address>";
	}
}
?>