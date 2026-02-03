<?php
	$admin	=	$_SESSION['admin'];
	if ($admin != 1 )
	{
		exit('Vous n\'avez pas les droits administrateur.');
	} 
?>