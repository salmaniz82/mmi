<?php
/**
* Template Name: After Payment
*/



echo "After Payment";

?>



<form method="post" name="bookAppointment" action="<?php bloginfo('template_directory')?>/ajax.php">

	<input type="email" name="email">

	<input type="text" name="full_name">

	<button type="submit">Send</button>
	
	
</form>