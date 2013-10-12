<?php
	//
	// Wib Wab Web- and ArrowQuick-specific definitions for WP Mu.
	// All constants _must_ begin with "QS_".
	//
	// Copyright (c) 2009 ArrowQuick Solutions LLC.
    // This file is part of WibWabWeb.
    // It cannot be copied or distributed to any third parties.
	//
	
	define('QS_NAME',         "Product Name");
	define('QS_SITE',         "example.com"); // no HTTP prefix!
	define('QS_VERSION',      "1.0");
	define('QS_YEAR_STARTED', 2009);
	// Note: Email address is stored in WordPress database
	//       and can be accessed through get_site_option("admin_email")
	
	define('QS_COMPANY_NAME',       "Company Name");
	define('QS_SHORT_COMPANY_NAME', "Company");
	define('QS_LEGAL_COMPANY_NAME', "Company Name, LLC");
	define('QS_COMPANY_URL',        "http://example.com");

	// Credentials for the payment API.
	define('QS_PAYMENT_USER', "000000");
	define('QS_PAYMENT_PASS', "password here");
	
	// Credentials for CTCT API.
	define('QS_CTCT_USER', "ctct_username");
	define('QS_CTCT_PASS', "password here");
	define('QS_CTCT_KEY',  "00000000-0000-0000-0000-000000000000");
	define('QS_CTCT_LIST', "/lists/4");
	
	// Credentials for Wordpress.com Stats API.
	define('QS_STATS_API', "000000000000");
?>