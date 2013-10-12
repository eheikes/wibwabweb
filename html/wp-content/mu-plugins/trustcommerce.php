<?php
    //
    // This file defines routines for integrating with
    //   the TrustCommerce payment API, specifically TC Citadel.
    // Currently only supports credit card transactions.
    //
    // For TC developer documentation, login to trustcommerce.com and
    //   go to Downloads to get the Developers Guide.
    //
    // Requires the cURL and BC Math extensions.
    // Requires QS_PAYMENT_USER and QS_PAYMENT_PASS constants
    //   to be defined.
    //
    // Copyright (c) 2011 ArrowQuick Solutions LLC.
    // Licensed under the GNU General Public License version 2
    //   (http://www.gnu.org/licenses/).
    //

	// for GetResponse()
	const PAYMENT_DATA = 1;
	const PAYMENT_INFO = 2;

	class TrustCommerce
	{
		protected $url      = "https://vault.trustcommerce.com/trans/";
		protected $curl     = null;		// cURL session object
		
		protected $errors   = array();	// last errors in ID => message format
		protected $response = array();	// parsed data from the last response
		protected $info     = array();	// info about the last response
		
		public function __construct()
		{
			$this->curl = curl_init();
			if (!$this->curl)
			{
				$this->errors = array();
				$this->errors[] = "There was a server error (Cannot create cURL session).";
			}
		}
		
		public function __destruct()
		{
			if ($this->curl) curl_close($this->curl);
		}
		
		public function LastErrors()
		{
			return (array)$this->errors;
		}
		
		public function GetResponse($what = PAYMENT_DATA)
		{
			if ($what == PAYMENT_DATA)
			{
				return $this->response;
			}
			else
			{
				return $this->info;
			}
		}
		
		// Create or edit an existing recurring transaction
		//   in TC Citadel.
		// $billingId -- billing ID to modify, or NULL to create one
		// $details -- associative array of payment info:
		//    amount -- the monthly charge
		//    cc -- credit card number (digits only)
		//    exp -- credit card expiration date (MMYY format)
		//    cvv -- credit card verification number
		//    address1 -- customer's address
		//    zip -- customer's zip code
		// Returns the Billing ID on success. Returns FALSE if errors.
		public function EditRecurring($billingId, $details)
		{
			// Setup the payment parameters.
			$params = $details;
			$params['action'] = "store";
			$params['avs'] = "y"; // perform address verification
			if (is_null($billingId)) // new recurring transaction
			{
				$params['cycle']   = "1m"; // every month
				$params['authnow'] = "y"; // immediate authorization for payment
				$start = time() + 60*60*24; // push back 1 day for TrustCommerce
				$params['start']   = date("Y-m-d", $start); // required, even though documentation says it's not
			}
			else
			{
				$params['billingid'] = $billingId;
			}
			
			// Format the data.
			if (isset($params['zip']))
			{
				$params['zip'] = preg_replace('#[^\d]#', '', $params['zip']);
			}
			if (isset($params['cc']))
			{
				$params['cc'] = preg_replace('#[^\d]#', '', $params['cc']);
			}

			$success = $this->Send($params);
			if ($success)
			{
				$data = $this->GetResponse();
				return @$data['billingid'];
			}
			else
			{
				return false;
			}
		}
		
		// Returns TRUE on success, FALSE otherwise.
		public function DeleteRecurring($billingId)
		{
			$params = array(
				"action"    => "unstore",
				"billingid" => $billingId
			);
			
			$success = $this->Send($params);
			return $success;
		}
		
		// Sends a (raw) transaction to the TrustCommerce server.
		// If WP_DEBUG is TRUE, transactions are sent in DEMO mode.
		// $params -- POST data as an associative array
		// Returns TRUE if successful, FALSE if there was an error
		//   (with the request itself or the business logic).
		public function Send($params = array())
		{
			// Sanity check if a cURL session exists.
			if (!$this->curl)
			{
				$this->errors[] = "There was a server error (No cURL session found).";
				return false;
			}
			
			// Sanity check to make sure username & password is set.
			if (!defined('QS_PAYMENT_USER')
			or  !defined('QS_PAYMENT_PASS'))
			{
				$this->errors[] = "There was a server error (Username and/or password for payment provider are not set).";
				return false;
			}
			
			// Form the parameter string.
			$params['custid']   = QS_PAYMENT_USER;
			$params['password'] = QS_PAYMENT_PASS;
			if (WP_DEBUG)
			{
				$params['demo'] = "y";
			}
			if (isset($params['amount']))
			{
				$params['amount'] = bcmul($params['amount'], 100, 0); // in cents
			}
			array_walk($params, array($this, "SanitizeParams"));
			$payload = http_build_query($params);
			
			// Set the cURL options.
			$success = curl_setopt_array($this->curl, array(
				CURLOPT_RETURNTRANSFER => true, // return response as output
				CURLOPT_HEADER         => false, // don't include headers in output
				CURLOPT_URL            => $this->url,
				CURLOPT_POST           => true,
				CURLOPT_POSTFIELDS     => $payload
			));
			if (!$success)
			{
				$this->errors[] = "There was a server error (Unable to set cURL options).";
				return false;
			}

			// Perform the request and save the response from the server.
			$response = curl_exec($this->curl);
			$this->response = parse_ini_string($response);
			$this->info     = curl_getinfo($this->curl);
			
			// Parse the response for errors.
			switch (strtolower($this->response['status']))
			{
				case "baddata": // invalid request data
					switch (strtolower($this->response['error']))
					{
						case "missingfields":
							$this->errors[] = "Some required fields were blank. Please check your information and resubmit.";
							break;
							
						case "merchantcantaccept":
							$this->errors[] = "We're sorry, but we don't accept the given type of card. Please contact us if you think this is in error.";
							break;
							
						case "extrafields": // includes fields not allowed
						case "badlength":
						case "badformat":
						case "mismatch": // conflicting fields
						case "dnsfailure": // TCLink only; shouldn't occur
						default:
							$this->errors[] = "The data sent to the payment processor was malformed or invalid (" . $this->response['error'] . "). Please contact us if the problem persists.";
							break;
					}
					return false;
					
				case "decline": // transaction not accepted
					$this->errors[] = "We're sorry, but your credit card was declined. Please check your information and contact us if you believe this to be in error.";
					return false;
					
				case "error": // routing/network issues
					$this->errors[] = "There was a problem connecting to the payment processor. Please contact us if the problem persists.";
					return false;
					
				case "approved": // accepted charge
				case "accepted": // accepted for later processing
				default:
					// all good
					break;
			}
			
			return true;
		}
		
		// Sanitize an associative array of parameters
		//   in accordance with TrustCommerce requirements.
		// (Basically, just printable ASCII characters.)
		protected function SanitizeParams(&$val, $key)
		{
			// Strip non-printable characters (including linebreaks).
			// Strip pipe character (|).
			$val = preg_replace('#[\x00-\x1f|]#', "", $val);
		}
	}
?>