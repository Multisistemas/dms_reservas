<?php
/**
 * Implementation of FolderAccess view
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
 * Class which outputs the html page for FolderAccess view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_FolderAccess extends SeedDMS_Bootstrap_Style {
	function printAccessModeSelection($defMode) { /* {{{ */
		print "<select name=\"mode\" class=\"form-control\">\n";
		print "\t<option value=\"".M_NONE."\"" . (($defMode == M_NONE) ? " selected" : "") . ">" . getMLText("access_mode_none") . "\n";
		print "\t<option value=\"".M_READ."\"" . (($defMode == M_READ) ? " selected" : "") . ">" . getMLText("access_mode_read") . "\n";
		print "\t<option value=\"".M_READWRITE."\"" . (($defMode == M_READWRITE) ? " selected" : "") . ">" . getMLText("access_mode_readwrite") . "\n";
		print "\t<option value=\"".M_ALL."\"" . (($defMode == M_ALL) ? " selected" : "") . ">" . getMLText("access_mode_all") . "\n";
		print "</select>\n";
	} /* }}} */

	function js() { /* {{{ */
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		header('Content-Type: application/javascript; charset=UTF-8');

?>

function checkForm()
{
	msg = new Array()
	if ((document.form1.userid.options[document.form1.userid.selectedIndex].value == -1) && 
		(document.form1.groupid.options[document.form1.groupid.selectedIndex].value == -1))
			msg.push("<?php printMLText("js_select_user_or_group");?>");
	if (msg != "") {
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			_timeout: 1500,
  	});
		return false;
	}
	else
		return true;
}
$(document).ready(function() {
	$('body').on('submit', '#form1', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
});
<?php
		
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$allUsers = $this->params['allusers'];
		$allGroups = $this->params['allgroups'];
		$rootfolderid = $this->params['rootfolderid'];

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar();
		$this->contentStart();
		echo $this->getDefaultFolderPathHTML($folder);

		/* Set owner ----------------------------------------------------- */

		if ($folder->getAccessMode($user) >= M_ALL) {
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-6\">";
		echo "<div class=\"box box-primary\">";
		echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".getMLText("set_owner")."</h3>";
    echo "</div>";
    echo "<div class=\"box-body\">";

    ?>
			<form class="form-horizontal" action="../op/op.FolderAccess.php">
			<?php echo createHiddenFieldWithKey('folderaccess'); ?>
			<input type="Hidden" name="action" value="setowner">
			<input type="Hidden" name="folderid" value="<?php print $folder->getID();?>">
			<select name="ownerid" class="form-control">
			<?php
					$owner = $folder->getOwner();
					foreach ($allUsers as $currUser) {
						if ($currUser->isGuest())
							continue;
						print "<option value=\"".$currUser->getID()."\"";
						if ($currUser->getID() == $owner->getID())
							print " selected";
						print ">" . htmlspecialchars($currUser->getLogin() . " - " . $currUser->getFullname()) . "</option>\n";
					}
			?>
			</select>
			<div class="box-footer">
				<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> <?php printMLText("save")?></button>
			</div>
			</form>
		<?php
			echo "</div>";
			echo "</div>";
			echo "</div>";
		} 

		/* Ends set owner ----------------------------------------------------- */


		if ($folder->getID() != $rootfolderid && $folder->getParent()){
			echo "<div class=\"col-md-6\">";
			echo "<div class=\"box box-info\">";
			echo "<div class=\"box-header with-border\">";
	    echo "<h3 class=\"box-title\">".getMLText("access_inheritance")."</h3>";
	    echo "</div>";
	    echo "<div class=\"box-body\">";
			
			if ($folder->inheritsAccess()) {
				printMLText("inherits_access_msg");
				?>
				  <p>
					<form class="form-horizontal" action="../op/op.FolderAccess.php">
				  <?php echo createHiddenFieldWithKey('folderaccess'); ?>
					<input type="hidden" name="folderid" value="<?php print $folder->getID();?>">
					<input type="hidden" name="action" value="notinherit">
					<input type="hidden" name="mode" value="copy">
					<button type="submit" class="btn btn-info" ><i class="fa fa-copy"></i> <?php printMLText("inherits_access_copy_msg")?></button>
					</form>
					</p>
					<p>
					<form action="../op/op.FolderAccess.php" style="display: inline-block;">
				  <?php echo createHiddenFieldWithKey('folderaccess'); ?>
					<input type="hidden" name="folderid" value="<?php print $folder->getID();?>">
					<input type="hidden" name="action" value="notinherit">
					<input type="hidden" name="mode" value="empty">
					<button type="submit" class="btn btn-primary" ><i class="fa fa-star"></i> <?php printMLText("inherits_access_empty_msg")?></button>
					</form>
					</p>
				<?php
				echo "</div>";
				echo "</div>";
				echo "</div>";
				echo "</div>";
				echo "</div>";
				$this->contentEnd();
				$this->mainFooter();		
				$this->containerEnd();
				$this->htmlEndPage();
				return;
			}
			?>
				<form action="../op/op.FolderAccess.php">
			  <?php echo createHiddenFieldWithKey('folderaccess'); ?>
				<input type="hidden" name="folderid" value="<?php print $folder->getID();?>">
				<input type="hidden" name="action" value="inherit">
				<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> <?php printMLText("does_not_inherit_access_msg")?></button>
				</form>
			<?php

			echo "</div>";
			echo "</div>";
			echo "</div>";
		}
		echo "</div>"; /* Ends first row */ 

		$accessList = $folder->getAccessList();
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-6\">";
		echo "<div class=\"box box-warning\">";
		echo "<div class=\"box-header with-border\">";
	  echo "<h3 class=\"box-title\">".getMLText("default_access")."</h3>";
	  echo "</div>";
	  echo "<div class=\"box-body\">";
		
		?>
		<form class="form-horizontal" action="../op/op.FolderAccess.php">
		  <?php echo createHiddenFieldWithKey('folderaccess'); ?>
			<input type="hidden" name="folderid" value="<?php print $folder->getID();?>">
			<input type="hidden" name="action" value="setdefault">
			<?php $this->printAccessModeSelection($folder->getDefaultAccess()); ?>
			<div class="box-footer">
				<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> <?php printMLText("save")?></button>	
			</div>
		</form>
		<?php

		echo "</div>";
		echo "</div>";
		echo "</div>";

		echo "<div class=\"col-md-6\">";
		echo "<div class=\"box box-success\">";
		echo "<div class=\"box-header with-border\">";
	  echo "<h3 class=\"box-title\">".getMLText("edit_existing_access")."</h3>";
	  echo "</div>";
	  echo "<div class=\"box-body\">";

		if ((count($accessList["users"]) != 0) || (count($accessList["groups"]) != 0)) {

			print "<div class=\"table-responsive\">";
			print "<table class=\"table\">";

			foreach ($accessList["users"] as $userAccess) {
				$userObj = $userAccess->getUser();
				print "<tr>\n";
				print "<td><i class=\"fa fa-user\"></i></td>\n";
				print "<td>". htmlspecialchars($userObj->getFullName()) . "</td>\n";
				print "<form action=\"../op/op.FolderAccess.php\">\n";
				echo createHiddenFieldWithKey('folderaccess')."\n";
				print "<input type=\"hidden\" name=\"folderid\" value=\"".$folder->getID()."\">\n";
				print "<input type=\"hidden\" name=\"action\" value=\"editaccess\">\n";
				print "<input type=\"hidden\" name=\"userid\" value=\"".$userObj->getID()."\">\n";
				print "<td>\n";
				$this->printAccessModeSelection($userAccess->getMode());
				print "</td>\n";
				print "<td>\n";
				print "<button type=\"submit\" class=\"btn btn-sm btn-info\"><i class=\"fa fa-save\"></i> ".getMLText("save")."</button>";
				print "</td>\n";
				print "</form>\n";
				print "<form action=\"../op/op.FolderAccess.php\">\n";
				echo createHiddenFieldWithKey('folderaccess')."\n";
				print "<input type=\"hidden\" name=\"folderid\" value=\"".$folder->getID()."\">\n";
				print "<input type=\"hidden\" name=\"action\" value=\"delaccess\">\n";
				print "<input type=\"hidden\" name=\"userid\" value=\"".$userObj->getID()."\">\n";
				print "<td>\n";
				print "<button type=\"submit\" class=\"btn btn-sm btn-danger\"><i class=\"fa fa-times\"></i> ".getMLText("delete")."</button>";
				print "</td>\n";
				print "</form>\n";
				print "</tr>\n";
			}

			foreach ($accessList["groups"] as $groupAccess) {
				$groupObj = $groupAccess->getGroup();
				$mode = $groupAccess->getMode();
				print "<tr>";
				print "<td><i class=\"fa fa-group\"></i></td>";
				print "<td>". htmlspecialchars($groupObj->getName()) . "</td>";
				print "<form action=\"../op/op.FolderAccess.php\">";
				echo createHiddenFieldWithKey('folderaccess')."\n";
				print "<input type=\"hidden\" name=\"folderid\" value=\"".$folder->getID()."\">";
				print "<input type=\"hidden\" name=\"action\" value=\"editaccess\">";
				print "<input type=\"hidden\" name=\"groupid\" value=\"".$groupObj->getID()."\">";
				print "<td>";
				$this->printAccessModeSelection($groupAccess->getMode());
				print "</td>\n";
				print "<td><span class=\"actions\">\n";
				print "<button type=\"submit\" class=\"btn btn-sm btn-info\"><i class=\"fa fa-save\"></i> ".getMLText("save")."</button>";
				print "</span></td>\n";
				print "</form>";
				print "<form action=\"../op/op.FolderAccess.php\">\n";
				echo createHiddenFieldWithKey('folderaccess')."\n";
				print "<input type=\"hidden\" name=\"folderid\" value=\"".$folder->getID()."\">\n";
				print "<input type=\"hidden\" name=\"action\" value=\"delaccess\">\n";
				print "<input type=\"hidden\" name=\"groupid\" value=\"".$groupObj->getID()."\">\n";
				print "<td>";
				print "<button type=\"submit\" class=\"btn btn-sm btn-danger\"><i class=\"fa fa-times\"></i> ".getMLText("delete")."</button>";
				print "</td>\n";
				print "</form>";
				print "</tr>\n";
			}
			
			print "</table>";
			print "</div>";
		}
		?>
		<form action="../op/op.FolderAccess.php" id="form1" name="form1">
		<?php echo createHiddenFieldWithKey('folderaccess'); ?>
		<input type="hidden" name="folderid" value="<?php print $folder->getID()?>">
		<input type="hidden" name="action" value="addaccess">
		<table class="table-condensed">
		<tr>
		<td><?php printMLText("user");?>:</td>
		<td>
		<select name="userid" class="form-control">
		<option value="-1"><?php printMLText("select_one");?>
		<?php
				foreach ($allUsers as $userObj) {
					if ($userObj->isGuest()) {
						continue;
					}
					print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getLogin() . " - " . $userObj->getFullName()) . "</option>\n";
				}
		?>
		</select>
		</td>
		</tr>
		<tr>
		<td class="inputDescription"><?php printMLText("group");?>:</td>
		<td>
		<select name="groupid" class="form-control">
		<option value="-1"><?php printMLText("select_one");?>
		<?php
				foreach ($allGroups as $groupObj) {
					print "<option value=\"".$groupObj->getID()."\">" . htmlspecialchars($groupObj->getName()) . "\n";
				}
		?>
		</select>
		</td>
		</tr>
		<tr>
		<td class="inputDescription"><?php printMLText("access_mode");?>:</td>
		<td>
		<?php
				$this->printAccessModeSelection(M_READ);
		?>
		</td>
		</tr>
		<tr>
		<td></td>
		<td>
			<button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> <?php printMLText("add")?></button>	
		</td>
		</tr>
		</table>
		</form>

<?php

		print "</div>";
		print "</div>";
		print "</div>";
		print "</div>";
		print "</div>";
		
		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>
