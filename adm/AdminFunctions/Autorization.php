<?php

/**
 * @package		XG Project
 * @copyright	Copyright (c) 2008 - 2015
 * @license		http://opensource.org/licenses/gpl-3.0.html	GPL-3.0
 * @since		Version 2.10.0
 */

if(!defined('INSIDE')){ die(header("location:../../"));}

include_once ( 'LogFunction.php' );

if ( $user['authlevel'] < 1 )
{
	die();
}

$QueryModeration	=	read_config ( 'moderation' );
$QueryModerationEx  =   explode ( ";" , $QueryModeration );
$Moderator			=	explode ( "," , $QueryModerationEx[0] );
$Operator			=	explode ( "," , $QueryModerationEx[1] );
$Administrator		=	explode ( "," , $QueryModerationEx[2] );

if ( $user['authlevel'] == 1 )
{
	$Observation	=	$Moderator[0];
	$EditUsers		=	$Moderator[1];
	$ConfigGame		=	$Moderator[2];
	$ToolsCanUse	=	$Moderator[3];
	$LogCanWork		=	$Moderator[4];
}

if ( $user['authlevel'] == 2 )
{
	$Observation	=	$Operator[0];
	$EditUsers		=	$Operator[1];
	$ConfigGame		=	$Operator[2];
	$ToolsCanUse	=	$Operator[3];
	$LogCanWork		=	$Operator[4];
}

if ( $user['authlevel'] == 3 )
{
	$Observation	=	1;
	$EditUsers		=	1;
	$ConfigGame		=	1;
	$ToolsCanUse	=	1;
	$LogCanWork		=	$Administrator[0];
}

?>
