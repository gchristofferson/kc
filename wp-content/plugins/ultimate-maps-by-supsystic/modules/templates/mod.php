<?php
class templatesUms extends moduleUms {
    protected $_styles = array();
	private $_cdnUrl = '';
	
	public function __construct($d) {
		parent::__construct($d);
		$this->getCdnUrl();	// Init CDN URL
	}
	public function getCdnUrl() {
		if(empty($this->_cdnUrl)) {
			if(uriUms::isHttps()) {
				$this->_cdnUrl = 'https://supsystic.com/';
			} else {
				$this->_cdnUrl = 'http://cdn.supsystic.com/';
			}
		}
		return $this->_cdnUrl;
	}
    public function init() {
        if (is_admin()) {
			if($isAdminPlugOptsPage = frameUms::_()->isAdminPlugOptsPage()) {
				$this->loadCoreJs();
				$this->loadAdminCoreJs();
				$this->loadCoreCss();
				$this->loadJqueryUi();
				//$this->loadChosenSelects();
				frameUms::_()->addScript('adminOptionsUms', UMS_JS_PATH. 'admin.options.js', array(), false, true);
				add_action('admin_enqueue_scripts', array($this, 'loadMediaScripts'));
			}
			// Some common styles - that need to be on all admin pages - be careful with them
			frameUms::_()->addStyle('supsystic-for-all-admin-'. UMS_CODE, UMS_CSS_PATH. 'supsystic-for-all-admin.css');
		}
        parent::init();
    }
	public function loadMediaScripts() {
		if(function_exists('wp_enqueue_media')) {
			wp_enqueue_media();
		}
	}
	public function loadAdminCoreJs() {
		frameUms::_()->addScript('jquery-ui-dialog', '', array('jquery'));
		frameUms::_()->addScript('jquery-ui-slider', '', array('jquery'));
		frameUms::_()->addScript('wp-color-picker');
		frameUms::_()->addScript('icheck', UMS_JS_PATH. 'icheck.min.js');
		frameUms::_()->addScript('jquery-ui-autocomplete', '', array('jquery'));
		$this->loadTooltipstered();
		$this->loadChosenSelects();	// Init Chosen library
	}
	public function loadCoreJs() {
		static $loaded = false;

		if(!$loaded) {
			frameUms::_()->addScript('jquery');
			frameUms::_()->addScript('commonUms', UMS_JS_PATH. 'common.js', array('jquery'));
			frameUms::_()->addScript('coreUms', UMS_JS_PATH. 'core.js', array('jquery'));

			$ajaxurl = admin_url('admin-ajax.php');

			if(frameUms::_()->getModule('options')->get('ssl_on_ajax')) {
				$ajaxurl = uriUms::makeHttps($ajaxurl);
			}
			$defEngine = frameUms::_()->getModule('options')->get('def_engine');
			/*$engineReq = reqUms::getVar('ums_engine', 'get');
			if(!empty($engineReq)) {
				$defEngine = $engineReq;
			}*/
			$jsData = array(
				'siteUrl'					=> UMS_SITE_URL,
				'imgPath'					=> UMS_IMG_PATH,
				'cssPath'					=> UMS_CSS_PATH,
				'modPath'					=> UMS_MODULES_PATH,
				'loader'					=> UMS_LOADER_IMG,
				'close'						=> UMS_IMG_PATH. 'cross.gif', 
				'ajaxurl'					=> $ajaxurl,
				'UMS_CODE'					=> UMS_CODE,
				'isAdmin'					=> is_admin(),
				//'gmapApiUrl'				=> frameUms::_()->getModule('maps')->getView()->getApiUrl(),
				'engine'					=> $defEngine,
				'mapboxKey'					=> frameUms::_()->getModule('options')->get('mapbox_key'),
				'thunderforestKey'			=> frameUms::_()->getModule('options')->get('thunderforest_key'),
			);
			if(is_admin()) {
				$jsData['isPro'] = frameUms::_()->getModule('supsystic_promo')->isPro();
			}
			$jsData = dispatcherUms::applyFilters('jsInitVariables', $jsData);

			frameUms::_()->addJSVar('coreUms', 'UMS_DATA', $jsData);

			$loaded = true;
		}
	}
	public function loadCoreCss() {
		$this->_styles = array(
			'styleUms'			=> array('path' => UMS_CSS_PATH. 'style.css', 'for' => 'admin'), 
			'supsystic-uiUms'	=> array('path' => UMS_CSS_PATH. 'supsystic-ui.css', 'for' => 'admin'), 
			'dashicons'			=> array('for' => 'admin'),
			'bootstrap-alerts'	=> array('path' => UMS_CSS_PATH. 'bootstrap-alerts.css', 'for' => 'admin'),
			'bootstrap-cols'	=> array('path' => UMS_CSS_PATH. 'bootstrap-cols.css', 'for' => 'admin'),
			'icheck'			=> array('path' => UMS_CSS_PATH. 'jquery.icheck.css', 'for' => 'admin'),
			'wp-color-picker'	=> array('for' => 'admin'),
		);
		foreach($this->_styles as $s => $sInfo) {
			if(!empty($sInfo['path'])) {
				frameUms::_()->addStyle($s, $sInfo['path']);
			} else {
				frameUms::_()->addStyle($s);
			}
		}
		$this->loadFontAwesome();
	}
	public function loadJqueryUi() {
		static $loaded = false;
		if(!$loaded) {
			frameUms::_()->addStyle('jquery-ui', UMS_CSS_PATH. 'jquery-ui.min.css');
			frameUms::_()->addStyle('jquery-ui.structure', UMS_CSS_PATH. 'jquery-ui.structure.min.css');
			frameUms::_()->addStyle('jquery-ui.theme', UMS_CSS_PATH. 'jquery-ui.theme.min.css');
			frameUms::_()->addStyle('jquery-slider', UMS_CSS_PATH. 'jquery-slider.css');
			$loaded = true;
		}
	}
	public function loadTooltipstered() {
		frameUms::_()->addScript('tooltipster', $this->_cdnUrl. 'lib/tooltipster/jquery.tooltipster.min.js');
		frameUms::_()->addStyle('tooltipster', $this->_cdnUrl. 'lib/tooltipster/tooltipster.css');
	}
	public function loadSlimscroll() {
		frameUms::_()->addScript('jquery.slimscroll', $this->_cdnUrl. 'js/jquery.slimscroll.js');	// Don't use CDN here - as this lib is modified
	}
	public function loadCodemirror() {
		frameUms::_()->addStyle('ptsCodemirror', $this->_cdnUrl. 'lib/codemirror/codemirror.css');
		frameUms::_()->addStyle('codemirror-addon-hint', $this->_cdnUrl. 'lib/codemirror/addon/hint/show-hint.css');
		frameUms::_()->addScript('ptsCodemirror', $this->_cdnUrl. 'lib/codemirror/codemirror.js');
		frameUms::_()->addScript('codemirror-addon-show-hint', $this->_cdnUrl. 'lib/codemirror/addon/hint/show-hint.js');
		frameUms::_()->addScript('codemirror-addon-xml-hint', $this->_cdnUrl. 'lib/codemirror/addon/hint/xml-hint.js');
		frameUms::_()->addScript('codemirror-addon-html-hint', $this->_cdnUrl. 'lib/codemirror/addon/hint/html-hint.js');
		frameUms::_()->addScript('codemirror-mode-xml', $this->_cdnUrl. 'lib/codemirror/mode/xml/xml.js');
		frameUms::_()->addScript('codemirror-mode-javascript', $this->_cdnUrl. 'lib/codemirror/mode/javascript/javascript.js');
		frameUms::_()->addScript('codemirror-mode-css', $this->_cdnUrl. 'lib/codemirror/mode/css/css.js');
		frameUms::_()->addScript('codemirror-mode-htmlmixed', $this->_cdnUrl. 'lib/codemirror/mode/htmlmixed/htmlmixed.js');
	}
	public function loadJqTreeView() {
		frameUms::_()->addStyle('jqtree', UMS_CSS_PATH. 'jqtree.css');
		frameUms::_()->addScript('tree.jquery', UMS_JS_PATH. 'tree.jquery.js');
	}
	public function loadJqGrid() {
		static $loaded = false;
		if(!$loaded) {
			$this->loadJqueryUi();
			frameUms::_()->addScript('jq-grid', $this->_cdnUrl. 'lib/jqgrid/jquery.jqGrid.min.js');
			frameUms::_()->addStyle('jq-grid', $this->_cdnUrl. 'lib/jqgrid/ui.jqgrid.css');
			$langToLoad = utilsUms::getLangCode2Letter();
			$availableLocales = array('ar','bg','bg1251','cat','cn','cs','da','de','dk','el','en','es','fa','fi','fr','gl','he','hr','hr1250','hu','id','is','it','ja','kr','lt','mne','nl','no','pl','pt','pt','ro','ru','sk','sr','sr','sv','th','tr','tw','ua','vi');
			if(!in_array($langToLoad, $availableLocales)) {
				$langToLoad = 'en';
			}
			frameUms::_()->addScript('jq-grid-lang', $this->_cdnUrl. 'lib/jqgrid/i18n/grid.locale-'. $langToLoad. '.js');
			$loaded = true;
		}
	}
	public function loadFontAwesome() {
		frameUms::_()->addStyle('font-awesomeUms', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
	}
	public function loadChosenSelects() {
		frameUms::_()->addStyle('jquery.chosen', $this->_cdnUrl. 'lib/chosen/chosen.min.css');
		frameUms::_()->addScript('jquery.chosen', $this->_cdnUrl. 'lib/chosen/chosen.jquery.min.js');
	}
	public function loadJqplot() {
		static $loaded = false;
		if(!$loaded) {
			$jqplotDir = $this->_cdnUrl. 'lib/jqplot/';

			frameUms::_()->addStyle('jquery.jqplot', $jqplotDir. 'jquery.jqplot.min.css');

			frameUms::_()->addScript('jplot', $jqplotDir. 'jquery.jqplot.min.js');
			frameUms::_()->addScript('jqplot.canvasAxisLabelRenderer', $jqplotDir. 'jqplot.canvasAxisLabelRenderer.min.js');
			frameUms::_()->addScript('jqplot.canvasTextRenderer', $jqplotDir. 'jqplot.canvasTextRenderer.min.js');
			frameUms::_()->addScript('jqplot.dateAxisRenderer', $jqplotDir. 'jqplot.dateAxisRenderer.min.js');
			frameUms::_()->addScript('jqplot.canvasAxisTickRenderer', $jqplotDir. 'jqplot.canvasAxisTickRenderer.min.js');
			frameUms::_()->addScript('jqplot.highlighter', $jqplotDir. 'jqplot.highlighter.min.js');
			frameUms::_()->addScript('jqplot.cursor', $jqplotDir. 'jqplot.cursor.min.js');
			frameUms::_()->addScript('jqplot.barRenderer', $jqplotDir. 'jqplot.barRenderer.min.js');
			frameUms::_()->addScript('jqplot.categoryAxisRenderer', $jqplotDir. 'jqplot.categoryAxisRenderer.min.js');
			frameUms::_()->addScript('jqplot.pointLabels', $jqplotDir. 'jqplot.pointLabels.min.js');
			frameUms::_()->addScript('jqplot.pieRenderer', $jqplotDir. 'jqplot.pieRenderer.min.js');
			$loaded = true;
		}
	}
	public function loadMagicAnims() {
		static $loaded = false;
		if(!$loaded) {
			frameUms::_()->addStyle('jquery.jqplot', $this->_cdnUrl. 'css/magic.min.css');
			$loaded = true;
		}
	}
	public function loadDatePicker() {
		frameUms::_()->addScript('jquery-ui-datepicker');
	}
	public function loadBootstrapSimple() {
		static $loaded = false;
		if(!$loaded) {
			frameUms::_()->addStyle('bootstrap-simple', UMS_CSS_PATH. 'bootstrap-simple.css');
			$loaded = true;
		}
	}
	public function loadGoogleFont( $font ) {
		static $loaded = array();
		if(!isset($loaded[ $font ])) {
			frameUms::_()->addStyle('google.font.'. str_replace(array(' '), '-', $font), 'https://fonts.googleapis.com/css?family='. urlencode($font));
			$loaded[ $font ] = 1;
		}
	}
}
