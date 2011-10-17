<?php
 /**
 * INBOX Highrise PHP Library
 *
 * cURL Connection
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		INBOX Highrise PHP Library
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: CurlConnection.php 1007 2010-02-16 02:29:52Z marcan $
 */

class inbox_highrise_CurlConnection {
	/**
	 * Whether we should use https to connect to the server or not
	 * @var bool
	 */
	public $useHttps = false;
	/**
	 * Highrise account name
	 * @var string
	 */
	public $accountName = "";
	/**
	 * Highrise authorization
	 * @var string
	 */
	public $authToken = "";
	/**
	 * Curl connection handler
	 * @var resource
	 */
	protected $ch;

	/**
	 * enabling debugging
	 * @var bool
	 */
	public $debug = false;

	static public function defaultUrl( $class ) {
		if ( $class instanceof inbox_highrise_Contact || $class === "inbox_highrise_Contact" ) {
			return "/people";
		} elseif ( $class instanceof inbox_highrise_Company || $class === "inbox_highrise_Company" ) {
			return "/companies";
		} elseif ( $class instanceof inbox_highrise_Note || $class === "inbox_highrise_Note" ) {
			return "/notes";
		} elseif ( $class instanceof inbox_highrise_Task || $class === "inbox_highrise_Task" ) {
			return "/tasks";
		} elseif ( $class instanceof inbox_highrise_TaskCategory || $class === "inbox_highrise_TaskCategory" ) {
			return "/task_categories";
		} elseif ( $class instanceof inbox_highrise_Tag || $class === "inbox_highrise_Tag" ) {
			return "/tags";
		} elseif ( $class instanceof inbox_highrise_Me || $class === "inbox_highrise_Me" ) {
			return "/me";
		}
		throw new Exception( "Cannot find default highrise url for $class" );
	}

	public function __construct( $account, $token) {
		$this->accountName = $account;
		$this->authToken = $token;
		$this->ch = curl_init();
	}
	public function __destruct() {
		if ( $this->ch ) {
			curl_close( $this->ch );
			$this->ch = null;
		}
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

	/**
	 * Post the specified entity to the highrise site
	 * @param inbox_highrise_Object $object Entity to save
	 * @param string $url Base URL where to post the entity (for associations)
	 */
	public function postEntity( inbox_highrise_Object $object, $url = "" ) {
		if ( $object->id ) {
			$this->execute( "PUT", $url . self::defaultUrl( $object ) . "/$object->id.xml", $object->toXml() );
		} else {
			$str = $this->execute( "POST", $url . self::defaultUrl( $object ) . ".xml", $object->toXml() );
			$code = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );

			// if debug enabled output info
			if ($this->debug) {
				echo "<h1>code: $code</h1>";
				echo "<h2>XML</h2>";
				echo $object->toXml();
				echo "<h2>str</h1>";
				echo $str;
			}

			if ( $code == 201 ) {
				$xml = simplexml_load_string( $str );
				if ( $xml->id ) {
					$object->id = intval( (string)$xml->id );
				}
			} else if ( $url == "" ) {
				throw new Exception( "Could not post contact information: got HTTP return code $code." );
			}
		}
	}


	public function execute( $method, $url, $body = "" ) {
		curl_setopt_array( $this->ch, array(
			CURLOPT_HEADER			=> 0,
			CURLOPT_HTTPHEADER		=> array( "Content-type: application/xml" ),
			CURLOPT_USERPWD			=> "$this->authToken:X",
			CURLOPT_URL				=> ( $this->useHttps ? "https" : "http" ) . "://$this->accountName.highrisehq.com$url",
			CURLOPT_RETURNTRANSFER	=> 1,
		) );
		if ( $method == "POST" ) {
			curl_setopt( $this->ch, CURLOPT_POST, true );
		}
		if ( $body ) {
			curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $body ); //includes the xml request
		}
		$data = curl_exec( $this->ch );
		return $data;
	}

	public function accepted() {
		$me_xml = inbox_highrise_Me::load($this);
		return (bool)$me_xml->token;
	}
}
?>