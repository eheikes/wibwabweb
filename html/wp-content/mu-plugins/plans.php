<?php
	//
	// Service subscription plans
	//
	// Copyright (c) 2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//

	namespace ArrowQuick;
	
	// Returns an array with all service plans
	//   (including trial), indexed by type.
	// TODO "active" property not used yet.
	function GetSubscriptionPlans()
	{
		// Key name must match the DB `subscription_type` field!
		return array(
			"TRIAL" => array(
				"name"         => "Trial",
				"cost"         => 0.00,
				"storage"      => 50,
				"bandwidth"    => 0.5,
				"emails"       => 0,
				"admins"       => 1,
				"active"       => true,
			),
			"STARTER" => array(
				"name"         => "Starter",
				"cost"         => 9.95,
				"storage"      => 50,
				"bandwidth"    => 0.5,
				"emails"       => 1,
				"admins"       => 1,
				"active"       => true,
			),
			"BASIC" => array(
				"name"         => "Basic",
				"cost"         => 29.95,
				"storage"      => 100,
				"bandwidth"    => 1,
				"emails"       => 10,
				"admins"       => 10,
				"active"       => true,
			),
			"PRO" => array(
				"name"         => "Pro",
				"cost"         => 59.95,
				"storage"      => 500,
				"bandwidth"    => 10,
				"emails"       => 25,
				"admins"       => "unlimited",
				"priority"     => true,
				"active"       => true,
			),
			"PREMIUM" => array(
				"name"         => "Premium",
				"cost"         => 99.95,
				"storage"      => 1000,
				"bandwidth"    => 100,
				"emails"       => 50,
				"georedundant" => true,
				"admins"       => "unlimited",
				"priority"     => true,
				"active"       => true,
			),
		);
	}
?>