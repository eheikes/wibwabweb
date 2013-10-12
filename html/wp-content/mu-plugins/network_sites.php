<?php
	//
	// This file modifies the All Sites list under Network Admin.
	//
	// Copyright (c) 2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	
	class NetworkSites
	{
		public static function Init()
		{
			add_filter('wpmu_blogs_columns', array(__CLASS__, 'AddColumns'));
			add_action('manage_sites_custom_column', array(__CLASS__, 'ColumnData'), 10, 2);
			add_filter('manage_sites_sortable_columns', array(__CLASS__, 'SortableColumns')); // BUG Doesn't work -- what is the filter name to use?
		}
		
		// Add column headers.
		public static function AddColumns($columns)
		{
			$columns['subscription_plan']    = __('Plan');
			$columns['subscription_billing'] = __('Billing ID');

			return $columns;
		}
		
		public static function ColumnData($col, $blogId)
		{
			// Retrieve the subscription info.
			$subscription = \ArrowQuick\Account\GetSubscriptionInfo($blogId);
			if (!$subscription) return $col;
			
			if ($col == 'subscription_plan')
			{
				echo esc_html($subscription->name);
				if ($subscription->end_time)
				{
					echo "<br/>expires " . date('n/j/Y', $subscription->end_time);
				}
			}
			elseif ($col == 'subscription_billing')
			{
				if ($subscription->billing_id)
				{
					echo $subscription->billing_id;
					$timestamp = strtotime($subscription->credit_card_exp);
					$expires = $timestamp + 60*60*24;
					if ($expires - time() < 60*60*24*30) // expires within 1 month
					{
						echo "<br/><strong>expires " . date("n/Y", $timestamp) . "</strong>";
					}
				}
			}
			
			return $col;
		}

		function SortableColumns()
		{
			return array(
				// These are the WP defaults.
				'blogname'          => 'blogname',
				'lastupdated'       => 'lastupdated',
				'registered'        => 'registered',
				// Add "Plans" column.
				'subscription_plan' => 'subscription_plan',
			);
		}
	}
	add_action('init', array('NetworkSites', 'Init'));

?>