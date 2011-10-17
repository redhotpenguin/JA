<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Note Recording
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: Note.php 1000 2010-02-16 00:55:00Z marcan $
 */


class inbox_highrise_Note extends inbox_highrise_Object {
	/**
	 * Body
	 * @var string
	 */
	public $body = "";
	/**
	 * Subject (item the note is related to)
	 * @var mixed
	 */
	public $subject;

	public function __construct( $id = null, $body = "" ) {
		parent::__construct( $id );
		if ( $body ) $this->body = $body;
	}
	public function __toString() {
		return $this->body;
	}
	public function toXml() {
		return "<note>\r\n"
			. $this->xmlProperty( "id",				$this->id )
			. $this->xmlProperty( "body",			$this->body )
			. "</note>";
	}

}
?>