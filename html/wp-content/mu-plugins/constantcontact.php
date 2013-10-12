<?php
	//
	// Routines for interacting with the CTCT API.
	// http://developer.constantcontact.com
	//
	// Requires the cURL and SimpleXML extensions.
	// Requires QS_CTCT_USER, QS_CTCT_PASS, QS_CTCT_KEY,
	//   and QS_CTCT_LIST constants to be defined.
	//
	// Copyright (c) 2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	
	class ConstantContact
	{
		protected $host     = "https://api.constantcontact.com";
		protected $url      = "https://api.constantcontact.com/ws/customers/";
		protected $curl     = null;		// cURL session object
		protected $errors   = array();	// last errors in ID => message format
		
		public function __construct()
		{
			$this->curl = curl_init();
			if (!$this->curl)
			{
				$this->errors = array();
				$this->errors[] = "There was a server error (Cannot create cURL session).";
			}
			
			// Add CTCT username to the URL.
			$this->url = $this->url . QS_CTCT_USER;
		}
		
		public function __destruct()
		{
			if ($this->curl) curl_close($this->curl);
		}

		public function LastErrors()
		{
			return (array)$this->errors;
		}
		
		// Returns an array of the default cURL options.
		protected function DefaultOpts()
		{
			return array(
				CURLOPT_RETURNTRANSFER => true, // return response as output
				CURLOPT_HEADER         => false, // don't include headers in output
				CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
				CURLOPT_USERPWD        => QS_CTCT_KEY . "%" . QS_CTCT_USER. ":" . QS_CTCT_PASS,
			);
		}
		
		// Retrieves raw XML of the contact lists.
		// This is mainly for testing.
		// see http://community.constantcontact.com/t5/Documentation/Retrieving-a-Contact-List-Collection/ba-p/25067
		public function GetLists()
		{
			// Sanity check if a cURL session exists.
			if (!$this->curl)
			{
				$this->errors[] = "There was a server error (No cURL session found).";
				return false;
			}
			
			// Set the cURL options.
			$opts = $this->DefaultOpts();
			$opts[CURLOPT_URL] = $this->url . "/lists";
			$success = curl_setopt_array($this->curl, $opts);
			if (!$success)
			{
				$this->errors[] = "There was a server error (Unable to set cURL options).";
				return false;
			}

			// Perform the request and save the response from the server.
			$response = curl_exec($this->curl);
			$info = curl_getinfo($this->curl);
			if ($info['http_code'] >= 400)
			{
				$this->errors[] = "There was a server error ({$info['http_code']}).";
				return false;
			}

			return $response;
		}
		
		// Retrieves a contact.
		// Returns a SimpleXML node for the contact, or NULL if no
		//   matching contact was found (and FALSE if an error occurs).
		public function GetContact($email)
		{
			// Sanity check if a cURL session exists.
			if (!$this->curl)
			{
				$this->errors[] = "There was a server error (No cURL session found).";
				return false;
			}
			
			// Set the cURL options.
			// see http://community.constantcontact.com/t5/Documentation/Searching-for-a-Contact-by-Email-Address/ba-p/25123
			$opts = $this->DefaultOpts();
			$opts[CURLOPT_URL] = $this->url . "/contacts?email=" . urlencode(strtolower($email));
			$success = curl_setopt_array($this->curl, $opts);
			if (!$success)
			{
				$this->errors[] = "There was a server error (Unable to set cURL options).";
				return false;
			}

			// Perform the request and save the response from the server.
			$response = curl_exec($this->curl);
			$info = curl_getinfo($this->curl);
			if ($info['http_code'] >= 400)
			{
				$this->errors[] = "There was a server error ({$info['http_code']}).";
				return false;
			}
			
			// Parse the XML response for the ID.
			$dom = new SimpleXMLElement($response);
			$edit_link = (string)@$dom->entry->link['href'];
			if ($edit_link == "") return null;
			
			// Retrieve the full details of the contact.
			// see http://community.constantcontact.com/t5/Documentation/Obtaining-a-Contact-s-Information/ba-p/25057
			$opts[CURLOPT_URL] = $this->host . $edit_link;
			$success = curl_setopt_array($this->curl, $opts);
			if (!$success)
			{
				$this->errors[] = "There was a server error (Unable to set cURL options).";
				return false;
			}

			// Perform the request and save the response from the server.
			$response = curl_exec($this->curl);
			$info = curl_getinfo($this->curl);
			if ($info['http_code'] >= 400)
			{
				$this->errors[] = "There was a server error ({$info['http_code']}).";
				return false;
			}
			
			// Parse the XML response for the ID.
			$dom_full = new SimpleXMLElement($response);

			return $dom_full;
		}
		
		// Adds a contact to the contact list specified by QS_CTCT_LIST.
		// Returns TRUE if successful, FALSE if there was an error.
		public function AddContact($email)
		{
			// Sanity check if a cURL session exists.
			if (!$this->curl)
			{
				$this->errors[] = "There was a server error (No cURL session found).";
				return false;
			}
			
			// Sanity check to make sure email address is set.
			if ($email == "")
			{
				$this->errors[] = "An email address must be included.";
				return false;
			}
			
			// First check if the contact already exists.
			$contact = $this->GetContact($email);
			if ($contact === false) return false; // error occurred
			
			//
			// If the contact doesn't exist yet, add him/her.
			//
			if (is_null($contact))
			{
				// Define the XML payload.
				// see http://community.constantcontact.com/t5/Documentation/Creating-a-Contact/ba-p/25059
				// The <id>, <title>, <author>, and <updated> elements are ignored by CTCT.
				// TODO This can be changed to ACTION_BY_CONTACT, especially if we add an opt-in checkbox to the signup form.
				$list = $this->url . QS_CTCT_LIST;
				$xml = <<<XML
<entry xmlns="http://www.w3.org/2005/Atom">
  <title type="text"> </title>
  <updated>2008-07-23T14:21:06.407Z</updated>
  <author></author>
  <id>data:,none</id>
  <summary type="text">Contact</summary>
  <content type="application/vnd.ctct+xml">
	<Contact xmlns="http://ws.constantcontact.com/ns/1.0/">
	  <EmailAddress>$email</EmailAddress>
	  <OptInSource>ACTION_BY_CUSTOMER</OptInSource>
	  <ContactLists>
		<ContactList id="$list" />
	  </ContactLists>
	</Contact>
  </content>
</entry>
XML;
				
				// Set the cURL options.
				$opts = $this->DefaultOpts();
				$opts[CURLOPT_URL]        = $this->url . "/contacts";
				$opts[CURLOPT_POST]       = true;
				$opts[CURLOPT_POSTFIELDS] = $xml;
				$opts[CURLOPT_HTTPHEADER] = array("Content-type: application/atom+xml");
				$success = curl_setopt_array($this->curl, $opts);
				if (!$success)
				{
					$this->errors[] = "There was a server error (Unable to set cURL options).";
					return false;
				}

				// Perform the request and save the response from the server.
				$response = curl_exec($this->curl);
				$info = curl_getinfo($this->curl);
				if ($info['http_code'] >= 400)
				{
					$this->errors[] = "There was a server error ({$info['http_code']}).";
					return false;
				}
			}
			//
			// If the contact _does_ exist, add this list to that record.
			//
			else
			{
				// Define the XML payload.
				// see http://community.constantcontact.com/t5/Documentation/Adding-a-Contact-to-a-List/ba-p/25121
				// The <id>, <title>, <author>, and <updated> elements are ignored by CTCT?
				// TODO This can be changed to ACTION_BY_CONTACT, especially if we add an opt-in checkbox to the signup form.
				$list = $this->url . QS_CTCT_LIST;
				$id = (string)@$contact->id;
				$list_xml = "";
				if (isset($contact->content->Contact->ContactLists->ContactList))
				{
					foreach ($contact->content->Contact->ContactLists->ContactList as $contact_list)
					{
						$list_xml .= "<ContactList id=\"" . (string)@$contact_list['id'] . "\" />\n";
					}
				}
				$xml = <<<XML
<entry xmlns="http://www.w3.org/2005/Atom">
  <id>$id</id>
  <title type="text">Contact: $email</title>
  <updated>2008-04-25T19:29:06.096Z</updated>
  <author> </author>
  <content type="application/vnd.ctct+xml">
    <Contact xmlns="http://ws.constantcontact.com/ns/1.0/" id="$id">
      <EmailAddress>$email</EmailAddress>
      <OptInSource>ACTION_BY_CUSTOMER</OptInSource>
      <ContactLists>
        <ContactList id="$list" />
        $list_xml
      </ContactLists>
    </Contact>
  </content>
</entry>
XML;

				// Set the cURL options.
				$opts = $this->DefaultOpts();
				$edit_link = (string)@$contact->link['href'];
				$opts[CURLOPT_URL]           = $this->host . $edit_link;
				$opts[CURLOPT_CUSTOMREQUEST] = "PUT";
				$opts[CURLOPT_POSTFIELDS]    = $xml;
				$opts[CURLOPT_HTTPHEADER]    = array("Content-type: application/atom+xml");
				$success = curl_setopt_array($this->curl, $opts);
				if (!$success)
				{
					$this->errors[] = "There was a server error (Unable to set cURL options).";
					return false;
				}

				// Perform the request and save the response from the server.
				$response = curl_exec($this->curl);
				$info = curl_getinfo($this->curl);
				if ($info['http_code'] >= 400)
				{
					$this->errors[] = "There was a server error ({$info['http_code']}).";
					return false;
				}
			}

			return true;
		}
	}
?>