<?php
/**
 * Implementation of ViewFolder view
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
 * Include class to preview documents
 */
require_once("SeedDMS/Preview.php");
/**
 * Class which outputs the html page for ViewFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ViewFolder extends SeedDMS_Bootstrap_Style {
	function getAccessModeText($defMode) { /* {{{ */
		switch($defMode) {
			case M_NONE:
				return getMLText("access_mode_none");
				break;
			case M_READ:
				return getMLText("access_mode_read");
				break;
			case M_READWRITE:
				return getMLText("access_mode_readwrite");
				break;
			case M_ALL:
				return getMLText("access_mode_all");
				break;
		}
	} /* }}} */
	function printAccessList($obj) { /* {{{ */
		$accessList = $obj->getAccessList();
		if (count($accessList["users"]) == 0 && count($accessList["groups"]) == 0)
			return;
		$content = '';
		for ($i = 0; $i < count($accessList["groups"]); $i++)
		{
			$group = $accessList["groups"][$i]->getGroup();
			$accesstext = $this->getAccessModeText($accessList["groups"][$i]->getMode());
			$content .= $accesstext.": ".htmlspecialchars($group->getName());
			if ($i+1 < count($accessList["groups"]) || count($accessList["users"]) > 0)
				$content .= "<br />";
		}
		for ($i = 0; $i < count($accessList["users"]); $i++)
		{
			$user = $accessList["users"][$i]->getUser();
			$accesstext = $this->getAccessModeText($accessList["users"][$i]->getMode());
			$content .= $accesstext.": ".htmlspecialchars($user->getFullName());
			if ($i+1 < count($accessList["users"]))
				$content .= "<br />";
		}
		if(count($accessList["groups"]) + count($accessList["users"]) > 3) {
			$this->printPopupBox(getMLText('list_access_rights'), $content);
		} else {
			echo $content;
		}
	} /* }}} */
	function js() { /* {{{ */
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$orderby = $this->params['orderby'];
		$expandFolderTree = $this->params['expandFolderTree'];
		$enableDropUpload = $this->params['enableDropUpload'];
		header('Content-Type: application/javascript; charset=UTF-8');
		parent::jsTranslations(array('cancel', 'splash_move_document', 'confirm_move_document', 'move_document', 'splash_move_folder', 'confirm_move_folder', 'move_folder'));
		
?>

function folderSelected(id, name) {
	window.location = '../out/out.ViewFolder.php?folderid=' + id;
}

function checkForm() {
	msg = new Array();
	if (document.form1.name.value == "") msg.push("<?php printMLText("js_no_name");?>");
	if (document.form1.comment.value == "") msg.push("<?php printMLText("js_no_comment");?>");
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

function checkForm2() {
	msg = new Array();
	if (document.form2.name.value == "") msg.push("<?php printMLText("js_no_name");?>");
	if (document.form2.comment.value == "") msg.push("<?php printMLText("js_no_comment");?>");
	/*if (document.form2.expdate.value == "") msg.push("<?php printMLText("js_no_expdate");?>");*/
	if (document.form2.theuserfile.value == "") msg.push("<?php printMLText("js_no_file");?>");
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

$(document).ajaxStart(function() { Pace.restart(); });
//  $('.ajax').click(function(){
//    $.ajax({url: '#', success: function(result){
//    $('.ajax-content').html('<hr>Ajax Request Completed !');
//  }});
//});

$(document).ready(function(){
	
	$('body').on('submit', '#form1', function(ev){
		if(!checkForm()) {
			ev.preventDefault();
		} else {
			$("#box-form1").append("<div class=\"overlay\"><i class=\"fa fa-refresh fa-spin\"></i></div>");
		}
	});

	$('body').on('submit', '#form2', function(ev){
		if(!checkForm2()){
			ev.preventDefault();
		} else {
			$("#box-form2").append("<div class=\"overlay\"><i class=\"fa fa-refresh fa-spin\"></i></div>");
		}
	});

	$("#form1").validate({
		invalidHandler: function(e, validator) {
			noty({
				text:  (validator.numberOfInvalids() == 1) ? "<?php printMLText("js_form_error");?>".replace('#', validator.numberOfInvalids()) : "<?php printMLText("js_form_errors");?>".replace('#', validator.numberOfInvalids()),
				type: 'error',
				dismissQueue: true,
				layout: 'topRight',
				theme: 'defaultTheme',
				timeout: 1500,
			});
		},
		messages: {
			name: "<?php printMLText("js_no_name");?>",
			comment: "<?php printMLText("js_no_comment");?>"
		},
	});

	$("#form2").validate({
		invalidHandler: function(e, validator) {
			noty({
				text:  (validator.numberOfInvalids() == 1) ? "<?php printMLText("js_form_error");?>".replace('#', validator.numberOfInvalids()) : "<?php printMLText("js_form_errors");?>".replace('#', validator.numberOfInvalids()),
				type: 'error',
				dismissQueue: true,
				layout: 'topRight',
				theme: 'defaultTheme',
				timeout: 1500,
			});
		},
		messages: {
			name: "<?php printMLText("js_no_name");?>",
			comment: "<?php printMLText("js_no_comment");?>",
			/*expdate: "<?php printMLText("js_no_expdate");?>",*/
			theuserfile: "<?php printMLText("js_no_file");?>",
		},
	});

	$("#add-folder").on("click", function(){
 		  $("#div-add-folder").show('slow');
  });

  $("#cancel-add-folder").on("click", function(){
 		  $("#div-add-folder").hide('slow');
  });

  $("#add-document").on("click", function(){
 		  $("#div-add-document").show('slow');
  });

  $(".cancel-add-document").on("click", function(){
 		  $("#div-add-document").hide('slow');
  });

  $(".move-doc-btn").on("click", function(ev){
  	id = $(ev.currentTarget).attr('rel');
 		$("#table-move-document-"+id).show('slow');
  });

  $(".cancel-doc-mv").on("click", function(ev){
  	id = $(ev.currentTarget).attr('rel');
 		$("#table-move-document-"+id).hide('slow');
  });

  $(".move-folder-btn").on("click", function(ev){
  	id = $(ev.currentTarget).attr('rel');
 		$("#table-move-folder-"+id).show('slow');
  });

  $(".cancel-folder-mv").on("click", function(ev){
  	id = $(ev.currentTarget).attr('rel');
 		$("#table-move-folder-"+id).hide('slow');
  });

  $("#btn-next-1").on("click", function(){
  	$("#nav-tab-1").removeClass("active");
  	$("#nav-tab-2").addClass("active");
  	$('html, body').animate({scrollTop: 0}, 800);
  });

  $("#btn-next-2").on("click", function(){
  	$("#nav-tab-2").removeClass("active");
  	$("#nav-tab-3").addClass("active");
  	$('html, body').animate({scrollTop: 0}, 800);
  });

  /* ---- For document previews ---- */

  $(".preview-doc-btn").on("click", function(){
  	$("#div-add-folder").hide();
		$("#div-add-document").hide();
  	$("#folder-content").hide();

  	var docID = $(this).attr("id");
  	var version = $(this).attr("rel");
  	$("#doc-title").text($(this).attr("title"));
  	$("#document-previewer").show('slow');
  	$("#iframe-charger").attr("src","../pdfviewer/web/viewer.html?file=..%2F..%2Fop%2Fop.Download.php%3Fdocumentid%3D"+docID+"%26version%3D"+version);
  });

  $(".close-doc-preview").on("click", function(){
  	$("#document-previewer").hide();
  	$("#iframe-charger").attr("src","");
  	$("#folder-content").show('slow');
  });
  
  /* ---- For datatables ---- */
	$(function () {
    $('#viewfolder-table').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": false,
      "autoWidth": true
    });
  });

});
<?php
		if ($enableDropUpload && $folder->getAccessMode($user) >= M_READWRITE) {
			echo "SeedDMSUpload.setUrl('../op/op.Ajax.php');";
			echo "SeedDMSUpload.setAbortBtnLabel('".getMLText("cancel")."');";
			echo "SeedDMSUpload.setEditBtnLabel('".getMLText("edit_document_props")."');";
			echo "SeedDMSUpload.setMaxFileSize(".SeedDMS_Core_File::parse_filesize(ini_get("upload_max_filesize")).");";
			echo "SeedDMSUpload.setMaxFileSizeMsg('".getMLText("uploading_maxsize")."');";
		}
		$this->printDeleteFolderButtonJs();
		$this->printDeleteDocumentButtonJs();
		$this->printKeywordChooserJs("form2");
		$this->printFolderChooserJs("form3");
		$this->printFolderChooserJs("form4");
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$orderby = $this->params['orderby'];
		$enableFolderTree = $this->params['enableFolderTree'];
		$enableClipboard = $this->params['enableclipboard'];
		$enableDropUpload = $this->params['enableDropUpload'];
		$expandFolderTree = $this->params['expandFolderTree'];
		$showtree = $this->params['showtree'];
		$cachedir = $this->params['cachedir'];
		$workflowmode = $this->params['workflowmode'];
		$enableRecursiveCount = $this->params['enableRecursiveCount'];
		$maxRecursiveCount = $this->params['maxRecursiveCount'];
		$previewwidth = $this->params['previewWidthList'];
		$timeout = $this->params['timeout'];
		$folderid = $folder->getId();
		$this->htmlAddHeader('<link href="../styles/'.$this->theme.'/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet">'."\n", 'css');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/plugins/datatables/jquery.dataTables.min.js"></script>'."\n", 'js');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/plugins/datatables/dataTables.bootstrap.min.js"></script>'."\n", 'js');
		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');
		
		echo $this->callHook('startPage');
		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))), "skin-blue sidebar-mini");
		$this->containerStart();
		$this->mainHeader();
		$this->mainSideBar($folder->getID(),0,0);
		$previewer = new SeedDMS_Preview_Previewer($cachedir, $previewwidth, $timeout);
		echo $this->callHook('preContent');
		$this->contentStart();		
		echo $this->getFolderPathHTML($folder);
		echo "<div class=\"row\">";

		//// Add Folder ////
		echo "<div class=\"col-md-12 div-hidden\" id=\"div-add-folder\">";
		echo "<div class=\"box box-success div-green-border\" id=\"box-form1\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".getMLText("add_subfolder")."</h3>";
    echo "<div class=\"box-tools pull-right\">";
    echo "<button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"remove\"><i class=\"fa fa-times\"></i></button>";
    echo "</div>";
    echo "<!-- /.box-tools -->";
    echo "</div>";
    echo "<!-- /.box-header -->";
    echo "<div class=\"box-body\">";
    ?>
    <form class="form-horizontal" action="../op/op.AddSubFolder.php" id="form1" name="form1" method="post">
			<?php echo createHiddenFieldWithKey('addsubfolder'); ?>
			<input type="hidden" name="folderid" value="<?php print $folder->getId();?>">
			<input type="hidden" name="showtree" value="<?php echo showtree();?>">
			<div class="box-body">

				<div class="form-group">
					<label class="col-sm-2 control-label"><?php printMLText("name");?>:</label>
					<div class="col-sm-10"><input class="form-control" type="text" name="name" size="60" required></div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label"><?php printMLText("comment");?>:</label>
					<div class="col-sm-10"><textarea class="form-control" name="comment" rows="4" cols="80"></textarea></div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label"><?php printMLText("sequence");?>:</label>
					<div class="col-sm-10">
						<?php $this->printSequenceChooser($folder->getSubFolders('s')); if($orderby != 's') echo "<br />".getMLText('order_by_sequence_off');?>
					</div>
				</div>
				<?php
					$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_folder, SeedDMS_Core_AttributeDefinition::objtype_all));
					if($attrdefs) {
						foreach($attrdefs as $attrdef) {
						?>
						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo htmlspecialchars($attrdef->getName()); ?>:</label>
							<div class="col-sm-10"><?php $this->printAttributeEditField($attrdef, '') ?></div>
						</div>
						<?php
						}
					}
				?>
				<div class="box-footer">
					<a id="cancel-add-folder" type="button" class="btn btn-default"><?php echo getMLText("cancel"); ?></a type="button">
					<button type="submit" class="btn btn-info pull-right"><i class="fa fa-save"></i> <?php printMLText("save")?></button>
				</div>
		</div>
		</form>
    <?php
    echo "</div>";
    echo "<!-- /.box-body -->";
    echo "</div>";
		echo "</div>";
		//// Add Document ////
		echo "<div class=\"col-md-12 div-hidden\" id=\"div-add-document\">";
		echo "<div class=\"box box-warning div-bkg-color\" id=\"box-form2\">";
    echo "<div class=\"box-header with-border\">";
    echo "<h3 class=\"box-title\">".getMLText("add_document")."</h3>";
    echo "<div class=\"box-tools pull-right\">";
    echo "<button type=\"button\" class=\"btn btn-box-tool\" data-widget=\"remove\"><i class=\"fa fa-times\"></i></button>";
    echo "</div>";
    echo "<!-- /.box-tools -->";
    echo "</div>";
    echo "<!-- /.box-header -->";
    echo "<div class=\"box-body\">";
    ?>

   	<form action="../op/op.AddDocument.php" enctype="multipart/form-data" method="post" id="form2" name="form2">
		<?php echo createHiddenFieldWithKey('adddocument'); ?>
		<input type="hidden" name="folderid" value="<?php print $folderid; ?>">
		<input type="hidden" name="showtree" value="<?php echo showtree();?>">
			<div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active" id="nav-tab-1"><a href="#tab_1" data-toggle="tab" aria-expanded="true">1 - <?php echo getMLText("document_infos"); ?></a></li>
          <li class="" id="nav-tab-2"><a href="#tab_2" data-toggle="tab" aria-expanded="false">2 - <?php echo getMLText("version_info"); ?></a></li>
          <li class="" id="nav-tab-3"><a href="#tab_3" data-toggle="tab" aria-expanded="false">3 - <?php echo getMLText("add_document_notify"); ?></a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
			    	<div class="form-group">
	            <label><?php echo getMLText("name"); ?>: <span class="is-required">*</span></label>
	            <input type="text" class="form-control" name="name" id="" placeholder="" required>
	          </div>
	          <div class="form-group">
	            <label><?php echo getMLText("comment"); ?>: <span class="is-required">*</span></label>
	            <textarea name="comment" class="form-control" rows="3" placeholder="" required></textarea>
	          </div>
	          <div class="form-group">
	            <label><?php echo getMLText("keywords");?>:</label>
	            <?php $this->printKeywordChooserHtml("form2");?>
	          </div>
	          <div class="form-group">
	            <label><?php printMLText("categories")?>:</label>
	            <select class="form-control chzn-select" name="categories[]" multiple="multiple" data-no_results_text="<?php printMLText('unknown_document_category'); ?>">
							<?php
								$categories = $dms->getDocumentCategories();
								foreach($categories as $category) {
									echo "<option value=\"".$category->getID()."\"";
									echo ">".$category->getName()."</option>";	
								}
							?>
							</select>
	          </div>
	          <div class="form-group">
	            <label><?php printMLText("sequence");?>:</label>
	            <?php $this->printSequenceChooser($folder->getDocuments('s')); if($orderby != 's') echo "<br />".getMLText('order_by_sequence_off'); ?>
	          </div>
	          <div class="form-group">
	          	<?php if($user->isAdmin()) { ?>
							<label><?php printMLText("owner");?>:</label>
							<select class="chzn-select form-control" name="ownerid">
							<?php
							$allUsers = $dms->getAllUsers();
							foreach ($allUsers as $currUser) {
								if ($currUser->isGuest())
									continue;
								print "<option value=\"".$currUser->getID()."\" ".($currUser->getID()==$user->getID() ? 'selected' : '')." data-subtitle=\"".htmlspecialchars($currUser->getFullName())."\"";
								print ">" . htmlspecialchars($currUser->getLogin()) . "</option>\n";
							}
							?>
							</select>
						<?php } ?>
	          </div>

	          <div class="form-group">
	          	<?php
							$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_document, SeedDMS_Core_AttributeDefinition::objtype_all));
							if($attrdefs) {
								foreach($attrdefs as $attrdef) {
									$arr = $this->callHook('editDocumentAttribute', null, $attrdef);
									if(is_array($arr)) {
										echo "<label>".$arr[0].":</label>";
										echo $arr[1];
									} else {
							?>
							<label><?php echo htmlspecialchars($attrdef->getName()); ?></label>
							<?php $this->printAttributeEditField($attrdef, ''); ?>
							<?php
									}
								}
							}
							?>
						</div>
						<div class="form-group">
							<label><?php printMLText("expires");?>: <span class="is-required">*</span></label>
			        <div class="input-append date span12" id="expirationdate" data-date="" data-date-format="yyyy-mm-dd" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>" data-checkbox="#expires">
			          <input class="form-control" size="16" name="expdate" type="text" value="">
			          <span class="add-on"></span>
			        </div>
			        <div class="checkbox">
			        	<label>
									<input type="checkbox" id="expires" name="expires" value="false" checked="true"><?php printMLText("does_not_expire");?>
				        </label>
	        		</div>
			    	</div>
			    	<div class="box-footer">
							<a type="button" class="btn btn-default cancel-add-document"><?php echo getMLText("cancel"); ?></a>
							<a id="btn-next-1" href="#tab_2" data-toggle="tab" type="button" class="btn btn-info pull-right"><?php echo getMLText("next"); ?> <i class="fa fa-arrow-right"></i></a>
						</div>
          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_2">
          	<div class="form-group">
	            <label><?php printMLText("version");?>:</label>
	            <input type="text" class="form-control" name="reqversion" value="1">
	          </div>
	          <?php $msg = getMLText("max_upload_size").": ".ini_get( "upload_max_filesize"); ?>
   					<?php $this->warningMsg($msg); ?>
	          <div class="form-group">
	            <label><?php printMLText("local_file");?>: <span class="is-required">*</span></label>
	            <?php
								$this->printFileChooser('userfile[]', false);
							?>
	          </div>
	          <div class="form-group">
	          	<label><?php printMLText("comment_for_current_version");?>:</label>
	          	<textarea class="form-control" name="version_comment" rows="3" cols="80"></textarea>
	          	<div class="checkbox">
	          		<label><input type="checkbox" name="use_comment" value="1" /> <?php printMLText("use_comment_of_document"); ?></label>
	          	</div>
	          </div>
						<?php
							$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_documentcontent, SeedDMS_Core_AttributeDefinition::objtype_all));
								if($attrdefs) {
									foreach($attrdefs as $attrdef) {
										$arr = $this->callHook('editDocumentAttribute', null, $attrdef);
										if(is_array($arr)) {
											echo "<label>".$arr[0].":</label>";
											echo $arr[1];
										} else {
									?>
										<label><?php echo htmlspecialchars($attrdef->getName()); ?></label>
										<?php $this->printAttributeEditField($attrdef, '', 'attributes_version') ?>
										<?php
										}
									}
								}
						if($workflowmode == 'advanced') { ?>
							<div class="form-group">
								<label><?php printMLText("workflow");?>:</label>
								<?php
								$mandatoryworkflows = $user->getMandatoryWorkflows();
								if($mandatoryworkflows) {
									if(count($mandatoryworkflows) == 1) { ?>
										<?php echo htmlspecialchars($mandatoryworkflows[0]->getName()); ?>
										<input type="hidden" name="workflow" value="<?php echo $mandatoryworkflows[0]->getID(); ?>">
									<?php
									} else { ?>
						        <select class="_chzn-select-deselect span9 form-control" name="workflow" data-placeholder="<?php printMLText('select_workflow'); ?>">
										<?php
											foreach ($mandatoryworkflows as $workflow) {
												print "<option value=\"".$workflow->getID()."\"";
												print ">". htmlspecialchars($workflow->getName())."</option>";
											} ?>
						        </select>
										<?php
											}
										} else { ?>
						        <select class="_chzn-select-deselect span9 form-control" name="workflow" data-placeholder="<?php printMLText('select_workflow'); ?>">
										<?php
											$workflows=$dms->getAllWorkflows();
											print "<option value=\"\">"."</option>";
											foreach ($workflows as $workflow) {
												print "<option value=\"".$workflow->getID()."\"";
												print ">". htmlspecialchars($workflow->getName())."</option>";
											} ?>
						        </select>
										<?php } ?>
									<br/>
									<?php $this->infoMsg(getMLText("add_doc_workflow_warning")); ?>
						<?php } else { echo $this->warningMsg("This theme only works with advanced workflows"); }?>
						</div>
						<div class="box-footer">
							<a type="button" class="btn btn-default cancel-add-document"><?php echo getMLText("cancel"); ?></a>
							<a id="btn-next-2" href="#tab_3" data-toggle="tab" aria-expanded="true" type="button" class="btn btn-info pull-right"><?php echo getMLText("next"); ?> <i class="fa fa-arrow-right"></i></a>
						</div>
          </div>
              <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_3">
            <div class="form-group">
            	<label><?php printMLText("individuals");?>:</label>
            	<select class="chzn-select span9 form-control" name="notification_users[]" multiple="multiple"">
								<?php
									$allUsers = $dms->getAllUsers("");
									foreach ($allUsers as $userObj) {
										if (!$userObj->isGuest() && $folder->getAccessMode($userObj) >= M_READ)
											print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getLogin() . " - " . $userObj->getFullName()) . "\n";
									}
								?>
							</select>
            </div>
            <div class="form-group">
							<label><?php printMLText("groups");?>:</label>
							<select class="chzn-select span9" name="notification_groups[]" multiple="multiple">
								<?php
									$allGroups = $dms->getAllGroups();
									foreach ($allGroups as $groupObj) {
										if ($folder->getGroupAccessMode($groupObj) >= M_READ)
											print "<option value=\"".$groupObj->getID()."\">" . htmlspecialchars($groupObj->getName()) . "\n";
									}
								?>
							</select>
						</div>
						<div class="box-footer">
							<a type="button" class="btn btn-default cancel-add-document"><?php echo getMLText("cancel"); ?></a>
							<button type="submit" class="btn btn-info pull-right"><i class="fa fa-save"></i> <?php echo getMLText("save"); ?></button>
						</div>	
          </div>
          <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
      </div>
   	</form>
    <?php
    echo "</div>";
    echo "<!-- /.box-body -->";
    echo "</div>";
		echo "</div>";
		//// Folder content ////
		$subFolders = $folder->getSubFolders($orderby);
		$subFolders = SeedDMS_Core_DMS::filterAccess($subFolders, $user, M_READ);
		$documents = $folder->getDocuments($orderby);
		$documents = SeedDMS_Core_DMS::filterAccess($documents, $user, M_READ);
		
		if ((count($subFolders) > 0)||(count($documents) > 0)){
			echo "<div class=\"col-md-12\" id=\"folder-content\">";
			echo "<div class=\"box box-primary\">";
	    echo "<div class=\"box-body no-padding\">";
			echo "<div class=\"table-responsive\">";
			$txt = $this->callHook('folderListHeader', $folder, $orderby);
			if(is_string($txt))
				echo $txt;
			else {
				print "<table id=\"viewfolder-table\" class=\"table table-hover table-striped table-condensed\">";
				print "<thead>\n<tr>\n";
				print "<th></th>\n";	
				print "<th>".getMLText("name")."</th>\n";
	//			print "<th>".getMLText("owner")."</th>\n";
				print "<th>".getMLText("status")."</th>\n";
	//			print "<th>".getMLText("version")."</th>\n";
				print "<th>".getMLText("action")."</th>\n";
				print "</tr>\n</thead>\n<tbody>\n";
			}
		  
		foreach($subFolders as $subFolder) {
			$txt = $this->callHook('folderListItem', $subFolder);
			if(is_string($txt))
				echo $txt;
			else {
				echo $this->folderListRow($subFolder);
			$formFol = "formFol".$subFolder->getID();
			?>
				<tr id="table-move-folder-<?php echo $subFolder->getID(); ?>" class="table-row-folder odd tr-border" style="display:none;">
					<td>
					<form action="../op/op.MoveFolder.php" name="<?php echo $formFol; ?>">
					<input type="hidden" name="folderid" value="<?php print $subFolder->getID();?>">
					</td>
					<td>
					<div>
							<label><?php printMLText("choose_target_folder");?>:</label>
							<?php $this->printFolderChooserHtml($formFol, M_READWRITE, $subFolder->getID(), null);?>
					</div>
					</td>
					<td>
						<a class="btn btn-default cancel-folder-mv" rel="<?php echo $subFolder->getID(); ?>"><?php echo getMLText("cancel"); ?></a>
						<input class="btn btn-success" type="submit" value="<?php printMLText("move"); ?>">
					</td>
					<td></td>
					</form>
				</tr>
				<?php
			}
		}
		foreach($documents as $document) {
			$document->verifyLastestContentExpriry();
			$txt = $this->callHook('documentListItem', $document, $previewer);
			if(is_string($txt))
				echo $txt;
			else {
				echo $this->documentListRow($document, $previewer);
			$formDoc = "formDoc".$document->getID();
				?>
					<tr id="table-move-document-<?php echo $document->getID(); ?>" class="table-row-document odd tr-border" style="display:none;">
					<td>
					<form action="../op/op.MoveDocument.php" name="<?php echo $formDoc; ?>">
					<input type="hidden" name="documentid" value="<?php print $document->getID();?>">
					</td>
					<td>
						<div>
							<label><?php printMLText("choose_target_folder");?>:</label>
							<?php $this->printFolderChooserHtml($formDoc, M_READWRITE, -1);?>
						</div>
					</td>
					<td>
						<a class="btn btn-default cancel-doc-mv" rel="<?php echo $document->getID(); ?>"><?php echo getMLText("cancel"); ?></a>
						<input class="btn btn-success" type="submit" value="<?php printMLText("move"); ?>">
					</td>
					<td></td>
					</form>
					</td>
					</tr>
				<?php
			}
		}
		if ((count($subFolders) > 0)||(count($documents) > 0)) {
			$txt = $this->callHook('folderListFooter', $folder);
			if(is_string($txt))
				echo $txt;
			else
				echo "</tbody>\n</table>\n";
		}
		echo "</div>";
		echo "</div>";
		echo "</div>";
		echo "</div>"; 
		} else {
			echo "<div class=\"col-md-12\">";
			$this->infoMsg(getMLText("empty_folder_list"));
			echo "</div>";
		}
		//// Document preview ////
		echo "<div class=\"col-md-12 div-hidden\" id=\"document-previewer\">";
		echo "<div class=\"box box-info\">";
		echo "<div class=\"box-header with-border box-header-doc-preview\">";
    echo "<span id=\"doc-title\" class=\"box-title\"></span>";
    echo "<span class=\"pull-right\">";
    //echo "<a class=\"btn btn-sm btn-primary\"><i class=\"fa fa-chevron-left\"></i></a>";
    //echo "<a class=\"btn btn-sm btn-primary\"><i class=\"fa fa-chevron-right\"></i></a>";
    echo "<a class=\"close-doc-preview btn btn-box-tool\"><i class=\"fa fa-times\"></i></a>";
    echo "</span>";
    echo "</div>";
    echo "<div class=\"box-body\">";
    echo "<iframe id=\"iframe-charger\" src=\"\" width=\"100%\" height=\"700px\"></iframe>";
    echo "</div>";
		echo "</div>";
		echo "</div>"; // End document preview
		?>
<?php
		echo "</div>\n"; // End of row
		echo "</div>\n"; // End of container

		echo $this->callHook('postContent');

		$this->contentEnd();
		$this->mainFooter();		
		$this->containerEnd();
		$this->htmlEndPage();
	} /* }}} */
}

?>