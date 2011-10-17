<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Task
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: Task.php 1000 2010-02-16 00:55:00Z marcan $
 */

class inbox_highrise_Task extends inbox_highrise_Object {
	/**
	 * Body
	 * @var string
	 */
	public $body = "";
	/**
	 * Category id
	 * @var int
	 */
	public $category = "";
	/**
	 * Subject (item the note is related to)
	 * @var mixed
	 */
	public $subject;
	/**
	 * Date due
	 * @var string
	 */
	public $dateDue = "";
	/**
	 * Recording
	 * @var int
	 */
	public $recording = "";

	public function __construct( $id = null, $body = "" ) {
		parent::__construct( $id );
		if ( $body ) $this->body = $body;
	}
	public function __toString() {
		return $this->body;
	}
	public function toXml() {
		return "<task>\r\n"
			. $this->xmlProperty( "id",				$this->id )
			. $this->xmlSubject( $this->subject )
			. $this->xmlProperty( "category-id",	$this->category )
			. $this->xmlProperty( "frame",			'specific' )
			. $this->xmlProperty( "due-at",			$this->dateDue,		"datetime" )
			. $this->xmlProperty( "body",			$this->body )
			. $this->xmlProperty( "recording-id",	$this->recording )
			. "</task>";
	}


	public function xmlSubject( $subject ) {
		return "";
	}
}
?>