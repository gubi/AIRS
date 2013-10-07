<!-- jquery-->
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery_ui_effects/ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery_ui_effects/ui/jquery.effects.core.js"></script>
<!-- Jquery ScrollTo -->
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery.scrollTo-1.4.2.js"></script>
<!-- Jquery copy -->
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/jquery_copy/jquery.copy.js"></script>
<!-- iPhone-password -->
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/iphone-password/jquery.iphone.password.js" charset="utf-8"></script>
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/iphone-password/caret.js" charset="utf-8"></script>
<!-- Apprise -->
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/apprise/apprise-1.5_edited.js" charset="utf-8"></script>
<link rel="stylesheet" href="<?php print $absolute_path; ?>common/js/apprise/apprise.min.css" type="text/css" media="screen" />
<?php
if (strtolower($GLOBALS["function_part"]) == "file" && $GLOBALS["user_level"] > 0){
	?>
	<link rel="stylesheet" href="<?php print $absolute_path; ?>common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
	<link rel="stylesheet" href="<?php print $absolute_path; ?>common/js/jquery_file_upload/jquery.fileupload-ui.css">
	<?php
}
?>
<!-- qTip -->
<link type="text/css" rel="stylesheet" href="<?php print $absolute_path; ?>common/js/qTip/jquery.qtip.css" />
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/qTip/jquery.qtip.js"></script>
<!-- Zoombox -->
<script type="text/javascript" src="<?php print $absolute_path; ?>common/js/zoombox/zoombox.js"></script>
<link href="<?php print $absolute_path; ?>common/js/zoombox/zoombox.css" rel="stylesheet" type="text/css" media="screen" />
<!-- Chosen -->
<link href="<?php print $absolute_path; ?>common/js/chosen/chosen.css" rel="stylesheet" type="text/css">
<script src="<?php print $absolute_path; ?>common/js/chosen/chosen.jquery.js" type="text/javascript" charset="utf-8"></script>

<!-- jQuery notify -->
<link href="<?php print $absolute_path; ?>common/js/Gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css">
<script src="<?php print $absolute_path; ?>common/js/Gritter/js/jquery.gritter.js" type="text/javascript" charset="utf-8"></script>