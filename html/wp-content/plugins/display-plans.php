<?php
/*
Plugin Name: Display Subscription Plans
Plugin URI: http://wibwabweb.com/pricing/
Description: Displays the subscription plans in a table. Use the [display-plans] shortcode. Requires the plans.php mu-plugin!
Version: 1.0
Author: ArrowQuick Solutions
Author URI: http://arrowquick.com
License: GPL2

    Copyright (c) 2011 ArrowQuick Solutions LLC.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

	function DisplaySubscriptionPlans($atts)
	{
		$html = "";

		if (!function_exists("ArrowQuick\\GetSubscriptionPlans"))
		{
			return "<p><strong>Error</strong> retrieving subscription plan information. Please check back later.</p>";
		}
		$plans = ArrowQuick\GetSubscriptionPlans();
		
		$html .= "<table class='pricing' border='0'>\n";
		$html .= "<col class='labels'></col>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<col class='";
			$html .= strtolower($key);
			if ($key == "BASIC")
			{
				$html .= " popular";
			}
			$html .= "'></col>\n";
		}
		$html .= "<thead>\n";
		$html .= "<tr>\n";
		$html .= "<th> </th>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<th>" . esc_html(@$plan['name']) . "</th>\n";
		}
		$html .= "</tr>\n";
		$html .= "</thead>\n";
		$html .= "<tbody>\n";
		
		// Storage
		$html .= "<tr class='alternate'>\n";
		$html .= "<td>Storage<sup><a href='#footnote-1'>1</a></sup></td>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<td>" . number_format(@$plan['storage']) . " MB</td>\n";
		}
		$html .= "</tr>\n";
		
		// Bandwidth
		$html .= "<tr>\n";
		$html .= "<td>Monthly Bandwidth</td>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<td>" . @$plan['bandwidth'] . " GB</td>\n";
		}
		$html .= "</tr>\n";
		
		// Email Accounts
		$html .= "<tr class='alternate'>\n";
		$html .= "<td>Email Accounts</td>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<td>";
			if (@$plan['emails'])
			{
				$html .= esc_html($plan['emails']);
				if (@$plan['georedundant'])
				{
					$html .= " geo-redundant<sup><a href='#footnote-2'>2</a></sup>";
				}
				if ($plan['emails'] > 1)
				{
					$html .= " mailboxes";
				}
				else
				{
					$html .= " mailbox";
				}
			}
			else
			{
				$html .= "--";
			}
			$html .= "</td>\n";
		}
		$html .= "</tr>\n";
		
		// Domains
		$html .= "<tr>\n";
		$html .= "<td>Custom Domain<sup><a href='#footnote-3'>3</a></sup><br /> (.com/.net/.org)</td>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<td>";
			if ($key == "TRIAL")
			{
				$html .= "--";
			}
			else
			{
				$html .= "included";
			}
			$html .= "</td>\n";
		}
		$html .= "</tr>\n";
		
		// Admin Users
		$html .= "<tr class='alternate'>\n";
		$html .= "<td>Administrative Users</td>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<td>";
			$html .= esc_html($plan['admins']) . " user account";
			if ($plan['admins'] != 1)
			{
				$html .= "s";
			}
			$html .= "</td>\n";
		}
		$html .= "</tr>\n";

		// Support
		$html .= "<tr>\n";
		$html .= "<td>Tech Support</td>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<td>";
			$html .= "Online tutorials,<br />";
			if (@$plan['priority'])
			{
				$html .= " <em>priority</em>";
			}
			$html .= " email support";
			$html .= "</td>\n";
		}
		$html .= "</tr>\n";
		
		// Admin Users
		$html .= "<tr class='alternate price'>\n";
		$html .= "<td>Price<sup><a href='#footnote-4'>4</a></sup></td>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<td>";
			if ($plan['cost'] == 0)
			{
				$html .= "<strong>FREE</strong> for 30 days";
			}
			else
			{
				$html .= "<strong>$" . number_format($plan['cost'], 2) . "</strong> monthly";
			}
			$html .= "</td>\n";
		}
		$html .= "</tr>\n";

		// Call To Action
		$html .= "<tr class='call-to-action'>\n";
		$html .= "<td></td>\n";
		foreach ($plans as $key => $plan)
		{
			$html .= "<td><a href='../sign-up'>Sign Up!</a></td>\n";
		}
		$html .= "</tr>\n";

		$html .= "</tbody>\n";
		$html .= "</table>\n";
		$html .= "<p class='footnote'><sup id='footnote-1'>1</sup> 1 MB is approximately 40 pages or 20 images. You can add an additional 1,000 MB for $10/month.</p>\n";
		$html .= "<p class='footnote'><sup id='footnote-2'>2</sup> Geo-redundancy provides a nationwide backup in the event of natural disaster.</p>\n";
		$html .= "<p class='footnote'><sup id='footnote-3'>3</sup> Additional domains are $1.50/month.</p>\n";
		$html .= "<p class='footnote'><sup id='footnote-4'>4</sup> All prices are in US Dollars.</p>\n";
		
		return $html;
	}
	add_shortcode("display-plans", "DisplaySubscriptionPlans");
?>