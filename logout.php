<?PHP
require ('mysql_connect.php');
  	      $_SESSION = array();
      if (isset($_COOKIE[session_name()])) setcookie(session_name(), '', time()-42000, '/');
	  @session_regenerate_id();
      @session_destroy();
		      header('Location:index.php');

?>