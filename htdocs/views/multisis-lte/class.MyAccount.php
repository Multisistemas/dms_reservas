<?php
/**
 * Implementation of MyAccount view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for MyAccount view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_MyAccount extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$enableuserimage = $this->params['enableuserimage'];
		$passwordexpiration = $this->params['passwordexpiration'];
		$httproot = $this->params['httproot'];
		$quota = $this->params['quota'];

		$this->htmlStartPage(getMLText("my_account"), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();

		?>
    <div class="gap-10"></div>
    <div class="row">
    <div class="col-md-12">
    <?php 

		if($quota > 0) {
			if(($remain = checkQuota($user)) < 0) {
				$this->warningMsg(getMLText('quota_warning', array('bytes'=>SeedDMS_Core_File::format_filesize(abs($remain)))));
			}
		}

		$this->startBoxPrimary(getMLText("user_info"));
		
		echo "<div class=\"row-fluid\">\n";
		if ($enableuserimage){
			echo "<div class=\"col-md-2\">\n";
			print ($user->hasImage() ? "<img class=\"userImage\" src=\"".$httproot . "out/out.UserImage.php?userid=".$user->getId()."\">" : getMLText("no_user_image"))."\n";
			echo "</div>\n";
			echo "<div class=\"col-md-10\">\n";
		} else {
			echo "<div class=\"col-md-12\">\n";
		}

		print "<table class=\"table table-bordered table-condensed\">\n";
		print "<tr>\n";
		print "<td class=\"header-bold\">".getMLText("name")." : </td>\n";
		print "<td>".htmlspecialchars($user->getFullName()).($user->isAdmin() ? " (".getMLText("admin").")" : "")."</td>\n";
		print "</tr>\n<tr>\n";
		print "<td class=\"header-bold\">".getMLText("user_login")." : </td>\n";
		print "<td>".$user->getLogin()."</td>\n";
		print "</tr>\n<tr>\n";
		print "<td class=\"header-bold\">".getMLText("email")." : </td>\n";
		print "<td>".htmlspecialchars($user->getEmail())."</td>\n";
		print "</tr>\n<tr>\n";

		if ($user->_comment != "client-admin") {
			print "<td class=\"header-bold\">".getMLText("biography")." : </td>\n";
			print "<td>".htmlspecialchars($user->getComment())."</td>\n";	
		}
		
		print "</tr>\n";
		if($passwordexpiration > 0) {
			print "<tr>\n";
			print "<td class=\"header-bold\">".getMLText("password_expiration")." : </td>\n";
			print "<td>".htmlspecialchars($user->getPwdExpiration())."</td>\n";
			print "</tr>\n";
		}
		print "<tr>\n";
		print "<td class=\"header-bold\">".getMLText("used_discspace")." : </td>\n";
		print "<td>".SeedDMS_Core_File::format_filesize($user->getUsedDiskSpace())."</td>\n";
		print "</tr>\n";
		if($quota > 0) {
			print "<tr>\n";
			print "<td class=\"header-bold\">".getMLText("quota")." : </td>\n";
			print "<td>".SeedDMS_Core_File::format_filesize($user->getQuota())."</td>\n";
			print "</tr>\n";
			if($user->getQuota() > $user->getUsedDiskSpace()) {

				if ($user->getUsedDiskSpace() == 0) {
					$used = 0;
					$free = 100-$used;
				} else {
					$used = (int) ($user->getUsedDiskSpace()/$user->getQuota()*100.0+0.5);
					$free = 100-$used;
				}
				
			} else {
				$free = 0;
				$used = 100;
			}
			print "<tr>\n";
			print "<td>\n";
			print "</td>\n";
			print "<td>\n";
?>
		<div class="progress-group">
			<span class="progress-text"><?php printMLText("disk_free_space"); ?></span>
			<span class="progress-number"><?php echo $free; ?>%</span>
			<div class="progress">
				<div class="progress-bar progress-bar-aqua" style="width: <?php echo $free; ?>%;"></div>	
			</div>
			<span class="progress-text"><?php printMLText("disk_used_space"); ?></span>
			<span class="progress-number"><?php echo $used; ?>%</span>
			<div class="progress">
				<div class="progress-bar progress-bar-red" style="width: <?php echo $used; ?>%;"></div>	
			</div>	  
		</div>
<?php
			print "</td>\n";
			print "</tr>\n";
		}
		print "</table>\n";
		print "</div>\n";
		print "</div>\n";

		?>
		<div class="box-footer">
			<a href="/out/out.EditUserData.php" type="button" class="btn btn-success"><i class="fa fa-pencil"></i> <?php printMLText("edit_user_details"); ?></a>
			<a href="/out/out.EditUserPassword.php" type="button" class="btn btn-primary"><i class="fa fa-pencil"></i> <?php printMLText("change_password"); ?></a>
		</div>
		<?php $this->endsBoxPrimary(); ?>

    </div>
    </div>
    </div>
    <?php
		
    $this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
