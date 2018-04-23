<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

JPlugin::loadLanguage( 'tpl_SG1' );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

<head>

<jdoc:include type="head" />



<link rel="stylesheet" href="templates/system/css/system.css" type="text/css" />

<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/template.css" type="text/css" />

<!-	
window.defaultStatus=""; 
 function ocultar_link() { 
 ocultar=document.getElementsByTagName("a"); 
 for (i=0;i<ocultar.length;i++) 
     ocultar.item(i).onmouseover=new Function("window.status='';return true");  
 }  
<![endif]-->

<!--[if lte IE 6]>

<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/ie6.css" type="text/css" />

<![endif]-->



</head>

<body id="body_bg">
	

	<div id="page_bg">
		<div class="separator">
            <table width="100%" align="center" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td width="150" bgcolor="#F0F0F0"></td>
                <td>
                <table width="960" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td colspan="2" bgcolor="#FFFFFF"><img src="./templates/siteground-j15-81/Somos_SENIAT/header1.jpg"></td>
                        </tr>
                        <tr>
                            <td width="309" align="center" bgcolor="#FFFFFF"><a href="http://rrhh.seniat.gob.ve/somos_seniat"><img src="./templates/siteground-j15-81/Somos_SENIAT/somos_seniat.jpg" border="0"></a></td>
                            <td width="651" id="td_banner" align="right" bgcolor="#FFFFFF" background="./templates/siteground-j15-81/Somos_SENIAT/banner_somos_seniat.jpg"><iframe id="marco" src="./templates/siteground-j15-81/Somos_SENIAT/modelo3.htm" width="373" height="67" frameborder="0"></iframe></td>
                        </tr>
                    </tbody>
                </table>
            </tbody>
            </table>
     	</div>
    
    
		<div class="pill_m">

			<div id="pillmenu">

				<table cellpadding="0" cellspacing="0" width="580">

					<tr>

						<td>
							
							<div class="separator"><jdoc:include type="modules" name="user3" /></div>

						</td>

					</tr>

				</table>

			</div>

			<div id="search">

				<jdoc:include type="modules" name="user4" />

				<div class="clr"></div>

			</div>

			<div class="clr"></div>

		</div>

		<div id="header">

			<div id="blue_car"></div>

			<div id="red_car"></div>

			<div id="logo"><h1><a href="index.php"><?php echo $mainframe->getCfg('sitename') ;?></a></h1></div>

		</div>

		<!--center start-->

		<?php if($this->countModules('left') and $this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>

		<div class="col_2">

		<?php elseif($this->countModules('left') and !$this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>

		<div class="col_l">

		<?php elseif($this->countModules('right') and !$this->countModules('left') and JRequest::getCmd('layout') != 'form') : ?>

		<div class="col_r">

		<?php else: ?>

		<div class="col_full">

		<?php endif; ?>

			<div id="wrapper">

				<div id="content">
										
					<?php if($this->countModules('left') and JRequest::getCmd('layout') != 'form') : ?>
											
					

				  <div id="leftcolumn">
						
							

							<jdoc:include type="modules" name="left" style="rounded" />
				  </div>
													

						<?php endif; ?>

						<?php if($this->countModules('left') and $this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>

						<div id="maincolumn">

						<?php elseif($this->countModules('left') and !$this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>

						<div id="maincolumn_left">

						<?php elseif(!$this->countModules('left') and $this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>
						
						<div id="maincolumn_right">

						<?php else: ?>

						<div id="maincolumn_full">

						<?php endif; ?>

							<?php if($this->countModules('user1') and JRequest::getCmd('layout') != 'form') : ?>

										
							<div id="latest">
							
								<jdoc:include type="modules" name="user1" style="rounded" />								

							</div>

							<?php endif;?>

							<?php if($this->countModules('user2') and JRequest::getCmd('layout') != 'form') : ?>

							<div id="popular">

								<jdoc:include type="modules" name="user2" style="rounded" />

							</div>

							<?php endif; ?>

							<div class="clr"></div>

							

							<div class="nopad">

								<jdoc:include type="message" />

								<?php if($this->params->get('showComponent')) : ?>

									<jdoc:include type="component" />

								<?php endif; ?>

							</div>

						</div>

						

						<?php if($this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>

						<div id="rightcolumn" style="float:right;">

							<jdoc:include type="modules" name="right" style="rounded" />														

						</div>

					<?php endif; ?>

					<div class="clr"></div>

				</div>		

			</div>

		</div>

		<!--center end-->

		

		<!--footer start-->

		<div id="footer">

			<div id="f123">

				<div>

					<div style="text-align: center; padding: 10px 0 0;">

						<?php $wd123 = ''; include "templates.php"; ?>

					</div> 

					<div style=" padding: 5px 0; text-align: center; color: #ccc;">&nbsp;&copy;&nbsp;Copyright, SENIAT, Servicio Nacional Integrado de Administraci&oacute;n Aduanera y   Tributaria, todos los derechos reservados .<br><img src="http://tibisay.seniat.gob.ve/produccion/templates/siteground-j15-81/08000.gif" width="189" height="41" align="center">					</div>

				</div>

			</div>

		</div>

		<!--footer end-->	

	<jdoc:include type="modules" name="debug" />

	</div>

</body>

</html>

