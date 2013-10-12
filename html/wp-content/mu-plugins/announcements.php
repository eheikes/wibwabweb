<?php
    //
    // This file defines common routines for Announcements programming.
    //
    // Copyright (c) 2009 ArrowQuick Solutions LLC.
    // Licensed under the GNU General Public License version 2
    //   (http://www.gnu.org/licenses/).
    //

	namespace ArrowQuick;
	
	class Announcements
	{
		// Retrieve all the announcements that have a publication_start before (or equal to) today
		// and a publication_end after (or equal to) today
		static public function Get()
		{
			global $blog_id;
			global $wpdb;

			$sql = $wpdb->prepare(
				"SELECT `html` "
				. ",UNIX_TIMESTAMP(`publication_start`) AS `start_time` "
				. ",UNIX_TIMESTAMP(`publication_end`) AS `end_time` "
				. "FROM `announcements` "
				. "WHERE (UNIX_TIMESTAMP(`publication_start`) <= UNIX_TIMESTAMP(NOW())) "
				. "AND (UNIX_TIMESTAMP(`publication_end`) >= UNIX_TIMESTAMP(NOW())) "
				. "ORDER BY UNIX_TIMESTAMP(`publication_start`) DESC"
			);
			$data = $wpdb->get_results($sql);
			return $data;
		}
	}
?>