<?php

/**
 * @package		XG Project
 * @copyright	Copyright (c) 2008 - 2014
 * @license		http://opensource.org/licenses/gpl-3.0.html	GPL-3.0
 * @since		Version 2.10.0
 */

define('INSIDE'  		,   	 TRUE);
define('INSTALL'        ,        TRUE);
define('XGP_ROOT'		, 	  './../');

include_once(XGP_ROOT . 'global.php');
include_once('databaseinfos.php');
include_once('migration.php');

$Mode     = isset ( $_GET['mode'] ) ? $_GET['mode'] : NULL;
$Page     = isset ( $_GET['page'] ) ? $_GET['page'] : NULL;
$phpself  = $_SERVER['PHP_SELF'];
$nextpage = $Page + 1;

if(version_compare(PHP_VERSION, "5.2.0", "<"))
	die("¡Error! Tu servidor debe tener al menos php 5.2.0");

if (empty($Mode)) { $Mode = 'intro'; }
if (empty($Page)) { $Page = 1;       }

switch ($Mode)
{
	case'license':
		$frame  = parsetemplate(gettemplate('install/ins_license'), FALSE);
	break;
	case 'intro':
		$frame  = parsetemplate(gettemplate('install/ins_intro'), FALSE);
	break;
	case 'ins':
		if ($Page == 1) {
			if (isset($_GET['error']) && $_GET['error'] == 1)
			{
				message ("La conexión a la base de datos a fallado","?mode=ins&page=1", 3, FALSE, FALSE);
			}
			elseif (isset($_GET['error']) && $_GET['error'] == 2)
			{
				message ("El fichero config.php no puede ser sustituido, no tenia acceso chmod 777","?mode=ins&page=1", 3, FALSE, FALSE);
			}

			$frame  = parsetemplate ( gettemplate ('install/ins_form'), FALSE);
		}
		elseif ($Page == 2)
		{
			$host   = $_POST['host'];
			$user   = $_POST['user'];
			$pass   = $_POST['passwort'];
			$prefix = $_POST['prefix'];
			$db     = $_POST['db'];

			$connection = @mysql_connect($host, $user, $pass);
			if (!$connection)
			{
				header ( 'location:?mode=ins&page=1&error=1' );
				exit();
			}

			$dbselect = @mysql_select_db($db);
			if (!$dbselect)
			{
				header ( 'location:?mode=ins&page=1&error=1' );
				exit();
			}

			$numcookie = mt_rand(1000, 1234567890);
			$dz = fopen("../config.php", "w");
			if (!$dz)
			{
				header ( 'location:?mode=ins&page=1&error=2' );
				exit();
			}

			$parse['first']	= "Conexión establecida con éxito...";

			fwrite($dz, "<?php\n");
			fwrite($dz, "if(!defined(\"INSIDE\")){ header(\"location:".XGP_ROOT."\"); }\n");
			fwrite($dz, "\$dbsettings = Array(\n");
			fwrite($dz, "\"server\"     => \"".$host."\", // MySQL server name.\n");
			fwrite($dz, "\"user\"       => \"".$user."\", // MySQL username.\n");
			fwrite($dz, "\"pass\"       => \"".$pass."\", // MySQL password.\n");
			fwrite($dz, "\"name\"       => \"".$db."\", // MySQL database name.\n");
			fwrite($dz, "\"prefix\"     => \"".$prefix."\", // Tables prefix.\n");
			fwrite($dz, "\"secretword\" => \"XGProject".$numcookie."\"); // Cookies.\n");
			fwrite($dz, "?>");
			fclose($dz);

			$parse['second']	= "Archivo config.php creado con éxito...";

			doquery ($QryTableAks        , 'aks'    	);
			doquery ($QryTableAlliance   , 'alliance'   );
			doquery ($QryTableBanned     , 'banned'     );
			doquery ($QryTableBuddy      , 'buddy'      );
			doquery ($QryTableErrors     , 'errors'     );
			doquery ($QryTableFleets     , 'fleets'     );
			doquery ($QryTableGalaxy     , 'galaxy'     );
			doquery ($QryTableMessages   , 'messages'   );
			doquery ($QryTableNotes      , 'notes'      );
			doquery ($QryTablePlanets    , 'planets'    );
			doquery ($QryTablePlugins    , 'plugins'    );
			doquery ($QryTableRw         , 'rw'         );
			doquery ($QryTableStatPoints , 'statpoints'	);
			doquery ($QryTableUsers      , 'users'  	);

			$parse['third']	= "Tablas creadas con éxito...";

			$frame  = parsetemplate(gettemplate('install/ins_form_done'), $parse);
		}
		elseif ($Page == 3)
		{
			if (isset($_GET['error']) && $_GET['error'] == 3)
				message ("¡Debes completar todos los campos!","?mode=ins&page=3", 2, FALSE, FALSE);

			$frame  = parsetemplate(gettemplate('install/ins_acc'), FALSE);
		}
		elseif ($Page == 4)
		{
			$adm_user   = $_POST['adm_user'];
			$adm_pass   = $_POST['adm_pass'];
			$adm_email  = $_POST['adm_email'];
			$md5pass    = md5($adm_pass);

			if (!$_POST['adm_user'])
			{
				header("Location: ?mode=ins&page=3&error=3");
				exit();
			}
			if (!$_POST['adm_pass'])
			{
				header("Location: ?mode=ins&page=3&error=3");
				exit();
			}
			if (!$_POST['adm_email'])
			{
				header("Location: ?mode=ins&page=3&error=3");
				exit();
			}

			$QryInsertAdm  = "INSERT INTO {{table}} SET ";
			$QryInsertAdm .= "`id`                = '1', ";
			$QryInsertAdm .= "`username`          = '". $adm_user ."', ";
			$QryInsertAdm .= "`email`             = '". $adm_email ."', ";
			$QryInsertAdm .= "`email_2`           = '". $adm_email ."', ";
			$QryInsertAdm .= "`ip_at_reg` 		  = '". $_SERVER["REMOTE_ADDR"] . "', ";
			$QryInsertAdm .= "`user_agent`        = '', ";
			$QryInsertAdm .= "`authlevel`         = '3', ";
			$QryInsertAdm .= "`id_planet`         = '1', ";
			$QryInsertAdm .= "`galaxy`            = '1', ";
			$QryInsertAdm .= "`system`            = '1', ";
			$QryInsertAdm .= "`planet`            = '1', ";
			$QryInsertAdm .= "`current_planet`    = '1', ";
			$QryInsertAdm .= "`register_time`     = '". time() ."', ";
			$QryInsertAdm .= "`password`          = '". $md5pass ."';";
			doquery($QryInsertAdm, 'users');

			$QryAddAdmPlt  = "INSERT INTO {{table}} SET ";
			$QryAddAdmPlt .= "`id_owner`          = '1', ";
			$QryAddAdmPlt .= "`galaxy`            = '1', ";
			$QryAddAdmPlt .= "`system`            = '1', ";
			$QryAddAdmPlt .= "`planet`            = '1', ";
			$QryAddAdmPlt .= "`last_update`       = '". time() ."', ";
			$QryAddAdmPlt .= "`planet_type`       = '1', ";
			$QryAddAdmPlt .= "`image`             = 'normaltempplanet02', ";
			$QryAddAdmPlt .= "`diameter`          = '12750', ";
			$QryAddAdmPlt .= "`field_max`         = '163', ";
			$QryAddAdmPlt .= "`temp_min`          = '47', ";
			$QryAddAdmPlt .= "`temp_max`          = '87', ";
			$QryAddAdmPlt .= "`metal`             = '500', ";
			$QryAddAdmPlt .= "`metal_perhour`     = '0', ";
			$QryAddAdmPlt .= "`metal_max`         = '1000000', ";
			$QryAddAdmPlt .= "`crystal`           = '500', ";
			$QryAddAdmPlt .= "`crystal_perhour`   = '0', ";
			$QryAddAdmPlt .= "`crystal_max`       = '1000000', ";
			$QryAddAdmPlt .= "`deuterium`         = '500', ";
			$QryAddAdmPlt .= "`deuterium_perhour` = '0', ";
			$QryAddAdmPlt .= "`deuterium_max`     = '1000000';";
			doquery($QryAddAdmPlt, 'planets');

			$QryAddAdmGlx  = "INSERT INTO {{table}} SET ";
			$QryAddAdmGlx .= "`galaxy`            = '1', ";
			$QryAddAdmGlx .= "`system`            = '1', ";
			$QryAddAdmGlx .= "`planet`            = '1', ";
			$QryAddAdmGlx .= "`id_planet`         = '1'; ";
			doquery($QryAddAdmGlx, 'galaxy');

			update_config ( 'stat_last_update' , time() );

			$frame  = parsetemplate(gettemplate('install/ins_acc_done'), '');
		}
		break;
	case'upgrade':

		$system_version	=	str_replace ( 'v' , '' , VERSION );

		if ( filesize ( '../config.php' ) == 0 )
		{
			die(message("¡Error! - Tu juego no está instalado","", "", FALSE, FALSE));
		}

		if ( SYSTEM_VERSION == $system_version )
		{
			die(message("¡Error! - No hay actualizaciones disponibles","", "", FALSE, FALSE));
		}

		if ($_POST)
		{
			$administrator	=	doquery("SELECT id
											FROM {{table}}
											WHERE password = '" . md5 ( $_POST['adm_pass'] ) . "' AND
													email = '" . mysql_escape_value ( $_POST['adm_email'] ) . "' AND
													authlevel = 3" , 'users' , TRUE );

			if ( !$administrator )
			{
				die(message("¡Error! - ¡El administrador introducido no existe o el usuario no tiene permisos administrativos!","index.php?mode=upgrade", "3", FALSE, FALSE));
			}

			if(filesize('../config.php') == 0)
			{
				die(message("¡Error! - Tu archivo config.php se encuentra vació o no configurado. En caso de no ser así verifica que su chmod sea de 777","", "", FALSE, FALSE));
			}
			else
			{
				include("../config.php");

				// ALL QUERYS NEEDED
				$Qry1 = "DELETE FROM `$dbsettings[prefix]config` WHERE `config_name` = 'VERSION'";
				$Qry2 = "INSERT INTO `$dbsettings[prefix]config` (`config_name`, `config_value`) VALUES ('VERSION', '".SYSTEM_VERSION."');";
				$Qry3 = "INSERT INTO `$dbsettings[prefix]config` (`config_name`, `config_value`) VALUES ('moderation', '1,0,0,1;1,1,0,1;');";
				$Qry4 = " ALTER TABLE `$dbsettings[prefix]banned` CHANGE `who` `who` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
							CHANGE `who2` `who2` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
							CHANGE `author` `author` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
							CHANGE `email` `email` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ";
				$Qry5 = "UPDATE `$dbsettings[prefix]config` SET `config_value` = '1,0,0,1,1;1,1,0,1,1;1;' WHERE `$dbsettings[prefix]config`.`config_name` = 'moderation';";
				$Qry6 = "ALTER TABLE `$dbsettings[prefix]planets` CHANGE `small_protection_shield` `small_protection_shield` TINYINT( 1 ) NOT NULL DEFAULT '0', CHANGE `big_protection_shield` `big_protection_shield` TINYINT( 1 ) NOT NULL DEFAULT '0'";
				$Qry7 = "UPDATE `$dbsettings[prefix]rw` SET `$dbsettings[prefix]rw`.`owners` = CONCAT(id_owner1,\",\",id_owner2)";
				$Qry8 = "ALTER TABLE `$dbsettings[prefix]rw`
  							DROP `id_owner1`,
  							DROP `id_owner2`;";
				$Qry9 = "ALTER TABLE $dbsettings[prefix]galaxy ADD `invisible_start_time` int(11) NOT NULL default '0'; ";
				$Qry10 = "ALTER TABLE `$dbsettings[prefix]users` DROP `rpg_espion`,DROP `rpg_constructeur`,DROP `rpg_scientifique`,DROP `rpg_commandant`,DROP `rpg_stockeur`,DROP `rpg_defenseur`,DROP `rpg_destructeur`,DROP `rpg_general`,DROP `rpg_empereur`;";
				$Qry11 = "DROP TABLE `$dbsettings[prefix]config`";

				$QrysArray	= NULL;

				switch($system_version)
				{
					case '2.9.0':
					case '2.9.1':
					case '2.9.2':
						$QrysArray	= array($Qry1, $Qry2, $Qry3, $Qry4, $Qry5, $Qry6, $Qry7, $Qry8, $Qry9, $Qry10, $Qry11);
						migrate_to_xml();
					break;
					case '2.9.3':
						$QrysArray	= array($Qry1, $Qry2, $Qry6, $Qry7, $Qry8, $Qry9, $Qry10, $Qry11);
						migrate_to_xml();
					break;
					case '2.9.4':
					case '2.9.5':
					case '2.9.6':
					case '2.9.7':
					case '2.9.8':
						$QrysArray	= array($Qry1, $Qry2, $Qry7, $Qry8, $Qry9, $Qry10, $Qry11);
						migrate_to_xml();
					break;
					case '2.9.9':
						$QrysArray	= array($Qry1, $Qry2, $Qry9, $Qry10, $Qry11 );
						migrate_to_xml();
					break;
					case '2.9.10':
						$QrysArray	= array($Qry1, $Qry2 , $Qry10, $Qry11);
						migrate_to_xml();
					break;
					case '2.10.0':
					case '2.10.1':
					case '2.10.2':
					case '2.10.3':
					case '2.10.4':
					case '2.10.5':
					case '2.10.6':
					case '2.10.7':
					case '2.10.8':
						update_config ( 'version' , SYSTEM_VERSION );
					break;
					default:
						message("¡La versión de tu proyecto no es compatible con XG Project, o estas intentando actualizar desde una versión más nueva!", "", "", FALSE, FALSE);
					break;
				}

				if ( $QrysArray != NULL )
				{
					foreach ( $QrysArray as $DoQuery )
					{
						mysql_query($DoQuery);
					}
				}

				message("XG Project finalizó la actualización de la versión " . $system_version . " a la versión " . SYSTEM_VERSION . " con éxito, para finalizar borra el directorio install y luego haz <a href=\"./../\">click aqui</a>", "", "", FALSE, FALSE);
			}
		}
		else
		{
			$parse['version']	=	SYSTEM_VERSION;
			$frame  = parsetemplate(gettemplate('install/ins_update'), $parse);
		}
		break;
	default:
}
$parse['ins_state']    = $Page;
$parse['ins_page']     = $frame;
$parse['dis_ins_btn']  = "?mode=$Mode&page=$nextpage";
display (parsetemplate (gettemplate('install/ins_body'), $parse), FALSE, '', TRUE, FALSE);
?>