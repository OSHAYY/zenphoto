<?php
/**
 * Loads Colorbox JS and CSS scripts for selected theme page scripts. Note that this plugin does not attach Colorbox to any element. You need to do this on your theme yourself. Visit http://colorpowered.com/colorbox/ about that.
 *
 * @author Stephen Billard (sbillard)
 *
 * @package plugins
 */

$plugin_description = gettext("Loads Colorbox JS and CSS scripts for selected theme page scripts. Note that this plugin does not attach Colorbox to any element. You need to do this on your theme yourself. Visit the <a href='http://colorpowered.com/colorbox/'>Colorbox website</a> about that.");
$plugin_author = 'Stephen Billard (sbillard)';
$plugin_version = '1.4.2';

$option_interface = 'colorbox_Options';

if (OFFSET_PATH) {
	zp_register_filter('admin_head','colorbox_css');
} else {
	global $_zp_gallery;
	if (getOption('colorbox_'.$_zp_gallery->getCurrentTheme().'_'.stripSuffix($_zp_gallery_page))) {
		zp_register_filter('theme_head','colorbox_css');
	}
}

function colorbox_css() {
	global $_zp_gallery;
	$theme = getOption('colorbox_theme');
	if(empty($theme)) { 
		$themepath = 'colorbox/themes/example1/colorbox.css';
	} else {
		if($theme == 'custom') {
			$themepath = 'colorbox/colorbox.css';
		} else {
			$themepath = 'colorbox/themes/'.$theme.'/colorbox.css';
		}
	}
	if (OFFSET_PATH) {
		$inTheme = false;
	} else {
		$inTheme = $_zp_gallery->getCurrentTheme();
	}
	$css = getPlugin($themepath,$inTheme,true);
	?>
	<script type="text/javascript" src="<?php echo FULLWEBPATH."/".ZENFOLDER.'/'.PLUGIN_FOLDER; ?>/colorbox/jquery.colorbox-min.js"></script>
	<link rel="stylesheet" href="<?php echo $css; ?>" type="text/css" />
	<?php
	$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
	if (stristr($navigator_user_agent, "msie") && !stristr($navigator_user_agent, '9')) {
		include(dirname(__FILE__).'/colorbox/colorbox_ie.css.php');
	}
}

class colorbox_Options {	

	function colorbox_Options() {
		//	These are best set by the theme itself!
		setOptionDefault('colorbox_theme','example1');
	}

	function getOptionsSupported() {
		$gallery = new Gallery();
		$opts  = array(gettext('Colorbox theme') => array('key' => 'colorbox_theme', 'type' => OPTION_TYPE_SELECTOR,
										'selections' => array(gettext('Example1') => "example1", gettext('Example2') => "example2", gettext('Example3') => "example3", gettext('Example4') => "example4",gettext('Example5') => "example5",gettext('Custom (theme based)') => "custom"),
										'desc' => gettext("The Colorbox script comes with 5 example themes you can select here. If you select <em>custom (within theme)</em> you need to place a folder <em>colorbox</em> containing a <em>colorbox.css</em> file and a folder <em>images</em> within the current theme to override to use a custom Colorbox theme."))
									);
		$exclude = array('404.php','themeoptions.php','theme_description.php');
		foreach (array_keys($gallery->getThemes()) as $theme) {
			$curdir = getcwd();
			$root = SERVERPATH.'/'.THEMEFOLDER.'/'.$theme.'/';
			chdir($root);
			$filelist = safe_glob('*.php');
			$list = array();
			foreach($filelist as $file) {
				if (!in_array($file,$exclude)) {
					$list[$script = stripSuffix(filesystemToInternal($file))] = 'colorbox_'.$theme.'_'.$script;
				}
			}
			chdir($curdir);
			$opts[$theme] = array('key' => 'colorbox_'.$theme.'_scripts', 'type' => OPTION_TYPE_CHECKBOX_ARRAY,
																	'checkboxes' => $list,
																	'desc' => gettext('The scripts for which Colorbox is enabled. {Should have been set by the themes!}')
											);
		}
		
		return $opts;
	}

	function handleOption($option, $currentValue) {
	}
}
?>