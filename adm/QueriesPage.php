<?php

/**
 * @package		XG Project
 * @copyright	Copyright (c) 2008 - 2014
 * @license		http://opensource.org/licenses/gpl-3.0.html	GPL-3.0
 * @since		Version 2.10.0
 */

define('INSIDE'  , TRUE);
define('INSTALL' , FALSE);
define('IN_ADMIN', TRUE);
define('XGP_ROOT', './../');

include(XGP_ROOT . 'global.php');

if ($user['authlevel'] != 3) die(message ($lang['404_page']));

$parse 	= $lang;
$Query	=	$_POST['querie'];

if ($_POST)
{
	$FinalQuery	=	str_replace("\'", "'", str_replace('\"', '"', $Query));
	mysql_query($FinalQuery);


	if(mysql_error())
	{
		$parse['display'] = "<tr><th><font color=red>".mysql_error()."</font></th></tr>";
	}
	else
	{
		$Log	.=	"\n".$lang['log_queries_title']."\n";
		$Log	.=	$lang['log_the_user'].$user['username']." ".$lang['log_queries_succes']."\n";
		$Log	.=	$Query."\n";
		LogFunction($Log, "GeneralLog", $LogCanWork);
		$parse['display'] = "<tr><th><font color=lime>".$lang['qe_succes']."</font></th></tr>";
	}
}

display(parsetemplate(gettemplate('adm/QueriesBody'), $parse), FALSE, '', TRUE, FALSE);
?>