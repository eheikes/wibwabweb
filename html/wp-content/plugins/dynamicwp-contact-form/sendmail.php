  <?php
  		if(isset($_POST['submit'])) {
          error_reporting(E_NOTICE);
          function valid_email($str)
          {
          return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
		     }
          if($_POST['name']!='' &&  $_POST['email']!='' && $_POST['subject']!='' && valid_email($_POST['email'])==TRUE && strlen($_POST['comment'])>1)
          {
		$to = preg_replace("([\r\n])", "", $_POST['receiver']);
		$from = preg_replace("([\r\n])", "", $_POST['email']);
		$subject = $_POST['subject'];
		$message = $_POST['comment'];
			  
			  $match = "/(bcc:|cc:|content\-type:)/i";
				if (preg_match($match, $to) ||
					preg_match($match, $from) ||
					preg_match($match, $message) || preg_match($match, $subject)) 
				{
				  die("Header injection detected.");
				}
              $headers = "From: ".$from."\r\n";
   			  $headers .= "Reply-to: ".$from."\r\n";
             
        if(mail($to, $subject, $message, $headers))
              {
                  echo 1; //SUCCESS
              }
              else {
                  echo 2; //FAILURE - server failure
              }
          }
          else {
       	  echo 3; //FAILURE - not valid email

          }
		  }else{
			 die("Direct access not allowed!");
		   }

      ?>



