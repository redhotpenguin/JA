<?php
 /**
 * INBOX Highrise PHP Library
 *
 * Including all parts of the Highrise API
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: highrise.php 1007 2010-02-16 02:29:52Z marcan $
 */

$dir = dirname( __FILE__ );

include_once( "$dir/Object.php" );
include_once( "$dir/CurlConnection.php" );

include_once( "$dir/Address.php" );
include_once( "$dir/Company.php" );
include_once( "$dir/Contact.php" );
include_once( "$dir/ContactData.php" );
include_once( "$dir/EmailAddress.php" );
include_once( "$dir/Me.php" );
include_once( "$dir/Note.php" );
include_once( "$dir/PhoneNumber.php" );
include_once( "$dir/Tag.php" );
include_once( "$dir/Task.php" );
include_once( "$dir/TaskCategory.php" );
include_once( "$dir/WebAddress.php" );
?>