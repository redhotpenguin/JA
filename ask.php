<?php

class Push_Highrise{

	var $highrise_url = 'https://newstorynetwork.highrisehq.com/'; // your highrise url, e.g. http://yourcompany.highrisehq.com
	var $api_token = 'b8db9c357bd0e9b9e03032013bbb08b1'; // your highrise api token; can be found under My Info
	var $task_assignee_user_id = '429780'; // user id of the highrise user who gets the task assigned 
	var $category = ''; // the category where deals will be assigned to
	
	var $errorMsg = "";
	
	function pushDeal($request){
		$curl = curl_init($this->highrise_url.'/deals.xml');
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERPWD,$this->api_token.':x');
		
		//Setup XML to POST
		curl_setopt($curl,CURLOPT_HTTPHEADER,Array("Content-Type: application/xml"));
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,'<deal>
			<name>'.htmlspecialchars($request['sSubject']).'</name>
			<price-type>fixed</price-type>
			<category-id type="integer">'.$category.'</category-id>
			<responsible-party-id type="integer">'.$this->task_assignee_user_id.'</responsible-party-id>
			<background>'.htmlspecialchars($request['sNotes']).'</background>
			<visible-to>Everyone</visible-to>
			<party-id type="integer">'.$this->_person_in_highrise($request).'</party-id>
		</deal>');
		
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$xml = curl_exec($curl);
		curl_close($curl);
		return '';
	}
	
	function pushNote($request){
		$curl = curl_init($this->highrise_url.'/notes.xml');
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERPWD,$this->api_token.':x');
		
		$bodyPrefix = "Contact request submitted from website";
				
		//Setup XML to POST
		curl_setopt($curl,CURLOPT_HTTPHEADER,Array("Content-Type: application/xml"));
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,'<note>
			<subject-id type="integer">'.$this->_person_in_highrise($request).'</subject-id>
			<subject-type>Party</subject-type>
			<body>'.$bodyPrefix.' '.htmlspecialchars($request['sSubject']).' - '.htmlspecialchars($request['sNotes']).'</body>
		</note>');
		
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$xml = curl_exec($curl);
		curl_close($curl);
		return '';
	}
	
	function pushTask($request){
		$curl = curl_init($this->highrise_url.'/tasks.xml');
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERPWD,$this->api_token.':x');
		
		$bodyPrefix = "Task subject"; // set the subject
				
		//Setup XML to POST
		curl_setopt($curl,CURLOPT_HTTPHEADER,Array("Content-Type: application/xml"));
		curl_setopt($curl,CURLOPT_POST,true);
		curl_setopt($curl,CURLOPT_POSTFIELDS,'<task>
			<subject-id type="integer">'.$this->_person_in_highrise($request).'</subject-id>
			<subject-type>Party</subject-type>
			<body>'.$bodyPrefix.' '.htmlspecialchars($request['sSubject']).' - '.htmlspecialchars($request['sNotes']).'</body>
			<frame>today</frame>
			<public type="boolean">true</public>
			<owner-id type="integer">'.$this->task_assignee_user_id.'</owner-id>
		</task>');
		
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$xml = curl_exec($curl);
		curl_close($curl);
		return '';
	}
	
	function pushContact($request){
		//Check that person doesn't already exist
		
		$id = $this->_person_in_highrise($request);
		if($id < 0){
				
			$curl = curl_init($this->highrise_url.'/people.xml');
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl,CURLOPT_USERPWD,$this->api_token.':x'); 
			
			curl_setopt($curl,CURLOPT_HTTPHEADER,Array("Content-Type: application/xml"));
			curl_setopt($curl,CURLOPT_POST,true);
			curl_setopt($curl,CURLOPT_POSTFIELDS,'<person>
				<first-name>'.htmlspecialchars($request['sFirstName']).'</first-name>
				<last-name>'.htmlspecialchars($request['sLastName']).'</last-name>
				<background>'.htmlspecialchars($request['staff_comment']).'</background>
				<company-name>'.htmlspecialchars($request['sCompany']).'</company-name>
				<contact-data>
					<email-addresses>
						<email-address>
							<address>'.htmlspecialchars($request['sEmail']).'</address>
							<location>Work</location>
						</email-address>
					</email-addresses>
				<phone-numbers>
					<phone-number>
						<number>'.htmlspecialchars($request['sPhone']).'</number>
						<location>Work</location>
					</phone-number>
				</phone-numbers>
				<addresses>
				    <address>
				      <city>'.htmlspecialchars($request['sCity']).'</city>
				      <country>'.htmlspecialchars($request['sCountry']).'</country>
				      <state>'.htmlspecialchars($request['sState']).'</state>
				      <street>'.htmlspecialchars($request['sStreet']).'</street>
				      <zip>'.htmlspecialchars($request['sZip']).'</zip>
				      <location>Work</location>
				    </address>
				  </addresses>
				</contact-data>
			</person>');
	
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
			curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
	
			$xml = curl_exec($curl);
			curl_close($curl);		
		
		}else{
			$this->errorMsg = "Person already in Highrise";
		}
		return '';
	}
	
	//Search for a person in Highrise 
	function _person_in_highrise($person){
		$curl = curl_init($this->highrise_url.'/people/search.xml?term='.urlencode($person['sFirstName'].' '.$person['sLastName']));
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERPWD,$this->api_token.':x'); //Username (api token, fake password as per Highrise api)

		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

		$xml = curl_exec($curl);
		curl_close($curl);	
		
		//Parse XML
		$people = simplexml_load_string($xml);
		echo($people);
		$id = '-1';
		foreach ($people->person as $person ) {
			if($person != null) {
				$id = $person->id;
			}	
		}
		return $id;
		
	}	
	
}

$p = new Push_Highrise();
$p->pushContact($_REQUEST);
$p->pushTask($_REQUEST);
$p->pushNote($_REQUEST);
header("Location: http://www.journalismaccelerator.com/question-thank-you/");

?>