<?php

/**
 * @file 
 * PDF Class to generate PDFs with native PHP. This class based on FPDF and FPDI.
 * 
 * A direct include of this class is not realy possible. The basic functions of drupal must be 
 * present.
 * 
 */


/** 
 * Get the depending classes.
 */
require_once views_pdf_get_library('tcpdf') . '/tcpdf.php';
require_once views_pdf_get_library('fpdi') .  '/fpdi2tcpdf_bridge.php';
require_once views_pdf_get_library('fpdi') . '/fpdi.php';


/**
 * The main class to generate the PDF.
 */
class PdfTemplate extends FPDI
{
  protected static $fontList = NULL;
  protected static $fontListClean = NULL;
  protected static $templateList = NULL;
  protected static $hyphenatePatterns = NULL;
  protected $defaultFontStyle = '';
  protected $defaultFontFamily = 'helvetica';
  protected $defaultFontSize = '11';
  protected $defaultTextAlign = 'L';
  protected $defaultFontColor = '000000';
  protected $defaultPageTemplateFiles = array();
  protected $mainContentPageNumber = 0;
  protected $rowContentPageNumber = 0;
  protected $defaultOrientation = 'P';
  protected $defaultFormat = 'A4';
  protected $addNewPageBeforeNextContent = false;
  protected $elements = array();
  protected $headerFooterData = array();
  protected $view = NULL;
  protected $headerFooterOptions = array();
  protected $lastWritingPage = 1;
  protected $lastWritingPositions;
  
  
  
  protected static $defaultFontList = array(
    'almohanad' => 'AlMohanad',
    'arialunicid0' => 'ArialUnicodeMS',
    'courier' => 'Courier',
    'courierb' => 'Courier Bold',
    'courierbi' => 'Courier Bold Italic',
    'courieri' => 'Courier Italic',
    'dejavusans' => 'DejaVuSans',
    'dejavusansb' => 'DejaVuSans-Bold',
    'dejavusansbi' => 'DejaVuSans-BoldOblique',
    'dejavusansi' => 'DejaVuSans-Oblique',
    'dejavusanscondensed' => 'DejaVuSansCondensed',
    'dejavusanscondensedb' => 'DejaVuSansCondensed-Bold',
    'dejavusanscondensedbi' => 'DejaVuSansCondensed-BoldOblique',
    'dejavusanscondensedi' => 'DejaVuSansCondensed-Oblique',
    'dejavusansmono' => 'DejaVuSansMono',
    'dejavusansmonob' => 'DejaVuSansMono-Bold',
    'dejavusansmonobi' => 'DejaVuSansMono-BoldOblique',
    'dejavusansmonoi' => 'DejaVuSansMono-Oblique',
    'dejavuserif' => 'DejaVuSerif',
    'dejavuserifb' => 'DejaVuSerif-Bold',
    'dejavuserifbi' => 'DejaVuSerif-BoldItalic',
    'dejavuserifi' => 'DejaVuSerif-Italic',
    'dejavuserifcondensed' => 'DejaVuSerifCondensed',
    'dejavuserifcondensedb' => 'DejaVuSerifCondensed-Bold',
    'dejavuserifcondensedbi' => 'DejaVuSerifCondensed-BoldItalic',
    'dejavuserifcondensedi' => 'DejaVuSerifCondensed-Italic',
    'freemono' => 'FreeMono',
    'freemonob' => 'FreeMonoBold',
    'freemonobi' => 'FreeMonoBoldOblique',
    'freemonoi' => 'FreeMonoOblique',
    'freesans' => 'FreeSans',
    'freesansb' => 'FreeSansBold',
    'freesansbi' => 'FreeSansBoldOblique',
    'freesansi' => 'FreeSansOblique',
    'freeserif' => 'FreeSerif',
    'freeserifb' => 'FreeSerifBold',
    'freeserifbi' => 'FreeSerifBoldItalic',
    'freeserifi' => 'FreeSerifItalic',
    'hysmyeongjostdmedium' => 'HYSMyeongJoStd-Medium-Acro',
    'helvetica' => 'Helvetica',
    'helveticab' => 'Helvetica Bold',
    'helveticabi' => 'Helvetica Bold Italic',
    'helveticai' => 'Helvetica Italic',
    'kozgopromedium' => 'KozGoPro-Medium-Acro',
    'kozminproregular' => 'KozMinPro-Regular-Acro',
    'msungstdlight' => 'MSungStd-Light-Acro',
    'stsongstdlight' => 'STSongStd-Light-Acro',
    'symbol' => 'Symbol',
    'times' => 'Times New Roman',
    'timesb' => 'Times New Roman Bold',
    'timesbi' => 'Times New Roman Bold Italic',
    'timesi' => 'Times New Roman Italic',
    'zapfdingbats' => 'Zapf Dingbats',
    'zarbold' => 'ZarBold'
  );
  
  /**
   * This method overrides the parent constructor method.
   * this is need to reset the default values.
   */
  public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false) {
    parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
    $this->defaultOrientation = $orientation;
    $this->defaultFormat = $format;
  }

  public function setDefaultFontSize($size) {
    $this->defaultFontSize = $size;
  }
  
  public function setDefaultFontFamily($family) {
    $this->defaultFontFamily = $family;
  }
  
  public function setDefaultFontStyle($style) {
    $this->defaultFontStyle = $style;
  }
  
  public function setDefaultTextAlign($style) {
    $this->defaultTextAlign = $style;
  }
  
  public function setDefaultFontColor($color) {
    $this->defaultFontColor = $color;
  }
  
  public function setDefaultPageTemplate($path, $key, $pageNumbering = 'main') {
    $this->defaultPageTemplateFiles[$key] = array(
      'path' => $path,
      'numbering' => $pageNumbering
    );
  }
  
  /**
   * This method must be overriden, in the other case, some
   * output is printed to the header.
   */
  function Header() {

  }
  
  /**
   * This method must be overriden, in the other case, some
   * output is printed to the footer.
   */
  function Footer() {
  
  }
  
  /**
   * Converts a hex color into an array with RGB colors.
   */
  public function convertHexColorToArray($hex) {
    if (strlen($hex) == 6) {
      $r = substr($hex, 0, 2);
      $g = substr($hex, 2, 2);
      $b = substr($hex, 4, 2);
      return array(hexdec($r), hexdec($g), hexdec($b));
    
    }
    elseif (strlen($hex) == 3) {
      $r = substr($hex, 0, 1);
      $g = substr($hex, 1, 1);
      $b = substr($hex, 2, 1);
      return array(hexdec($r), hexdec($g), hexdec($b));
    
    }
    else {
      return array();
    }
  }
  
  /**
   * Parse color input into an array.
   *
   * @param string $color Color entered by the user
   * @return array color as an array
   */
  public function parseColor($color) {
    $color = trim($color, ', ');
    $components = explode(',', $color);
    if (count($components) == 1) {
      return $this->convertHexColorToArray($color);
    }
    else {
      // Remove white spaces from comonents:
      foreach ($components as $id => $component) {
        $components[$id] = trim($component);
      }
      return $components;
    }
  }
  
  /**
   * This method draws a field on the PDF.
   */
  public function drawContent($row, $options, &$view = NULL, $key = NULL) {
    
    // Get the page dimensions
    $pageDim = $this->getPageDimensions();
    
    // Check if there is a minimum space defined. If so, then ensure
    // that we have enough space left on this page. If not force adding
    // a new one. 
    if (isset($options['render']['minimal_space'])) {
      $enoughtSpace = ($this->y + $this->bMargin + $options['render']['minimal_space']) < $pageDim['hk'];
    }
    else {
      $enoughtSpace = true;
    }
    
    
    // Check if there is a page, if not add it:
    if (!$enoughtSpace or $this->getPage() == 0 or $this->addNewPageBeforeNextContent == true) {
      $this->addNewPageBeforeNextContent = false;
      $this->addPage();
    }
    
    // Get the page dimenstions again, because it can be that a new
    // page was added with new dimensions.
    $pageDim = $this->getPageDimensions();
    
    // Determine the last writting y coordinate, if we are on a new 
    // page we need to reset it back to the top margin.
    if ($this->lastWritingPage != $this->getPage() or ($this->y + $this->bMargin) > $pageDim['hk']) {
      $last_writing_y_position = $this->tMargin;
    }
    else {
      $last_writing_y_position = $this->y;
    }
    
    // Determin the x and y coordinates
    if ($options['position']['object'] == 'last_position') {
      $x = $this->x + $options['position']['x'];
      $y = $this->y + $options['position']['y'];
    }
    elseif ($options['position']['object'] == 'page') {
      
      switch ($options['position']['corner']) {
        default:
        case 'top_left':
          $x = $options['position']['x']+40;
          $y = $options['position']['y']+$this->tMargin;
          break;
        
        case 'top_right':
          $x = $options['position']['x'] + $pageDim['wk'] - $this->rMargin;
          $y = $options['position']['y'] + $this->tMargin;    
          break;
        
        case 'bottom_left':
          $x = $options['position']['x'] + $this->rMargin;
          $y = $options['position']['y'] + $pageDim['hk'] - $this->bMargin;
        
          break;
        
        case 'bottom_right': 
          $x = $options['position']['x'] + $pageDim['wk'] - $this->rMargin;
          $y = $options['position']['y'] + $pageDim['hk'] - $this->bMargin;
        
          break;
      }
      
    }
    elseif (
      $options['position']['object'] == 'self' or 
      //$options['position']['object'] == 'last' or 
      preg_match('/field\_(.*)/', $options['position']['object'], $rs)
    ) {
      if ($options['position']['object'] == 'last') {
        $relative_to_element = $this->lastWritingElement;
      }
      elseif ($options['position']['object'] == 'self') {
        $relative_to_element = $key;
      }
      else {
        $relative_to_element = $rs[1];
      }     
      
      if (isset($this->elements[$relative_to_element])){
        
        switch ($options['position']['corner']) {
          default:
          case 'top_left':
            $x = $options['position']['x'] + $this->elements[$relative_to_element]['x'];
            $y = $options['position']['y'] + $this->elements[$relative_to_element]['y'];
            break;
        
          case 'top_right':
            $x = $options['position']['x'] + $this->elements[$relative_to_element]['x'] + $this->elements[$relative_to_element]['width'];
            $y = $options['position']['y'] + $this->elements[$relative_to_element]['y'];    
            break;
        
          case 'bottom_left':
            $x = $options['position']['x'] + $this->elements[$relative_to_element]['x'];
            $y = $options['position']['y'] + $this->elements[$relative_to_element]['y'] + $this->elements[$relative_to_element]['height'];
        
            break;
        
          case 'bottom_right': 
            $x = $options['position']['x'] + $this->elements[$relative_to_element]['x'] + $this->elements[$relative_to_element]['width'];
            $y = $options['position']['y'] + $this->elements[$relative_to_element]['y'] + $this->elements[$relative_to_element]['height'];
        
            break;
        }
        
        // Handle if the relative element is on another page. So using the 
        // the last writing position instead for y. 
        if ($this->getPage() != $this->elements[$relative_to_element]['page'] && $options['position']['object'] != 'self') {
          $this->setPage($this->elements[$relative_to_element]['page']);
        }
        elseif ($this->getPage() != $this->elements[$relative_to_element]['page'] && $options['position']['object'] == 'self') {
          $y = $y - $this->elements[$relative_to_element]['y'] + $last_writing_y_position;
          $this->SetPage($this->lastWritingPage);
        }
      }
      else {
        $x = $this->x;
        $y = $last_writing_y_position;
      }
      
    }
    
    // No position match (for example header/footer)
    else {
      // Render and then return
      if (is_object($view) && $key != NULL ) {
        $content = $view->field[$key]->theme($row);
      }

      return;
    }
    
    $this->SetX($x);
    $this->SetY($y);
        
    $this->renderRow($x, $y, $row, $options, $view, $key);
  }
  
  protected function renderRow($x, $y, $row, $options, &$view = NULL, $key = NULL) {
    
    $pageDim = $this->getPageDimensions();
    
    
    // Render the content if it is not already:
    if (is_object($view) && $key != NULL && is_object($view->field[$key])) {
      $content = $view->field[$key]->theme($row);
    }
    elseif(is_string($row)) {
	$titl="";
	if(arg(5)=="ASD" || arg(5)=="MAL"|| arg(5)=="PIK" || arg(5)=="YAT" || (arg(5)=="TLT" && arg(6)=="pdf") || (arg(3)=="compare" && arg(6)=="pdf")){
	$argg= arg(4);
	
   $viewName = 'surah_name';	  
   $view_s = views_get_view($viewName);
   $view_s->set_display('default');
   $view_s->set_arguments(array($argg));
   $view_s->execute();
   $result_s = $view_s->result;
    $titl =  '<br/><h3>Surah '.$argg.'. '.$result_s[0]->term_data_name.'</h3><br/>';
     }
	 if(arg(4)=="SOP")
	 {
	 $query = db_query("SELECT node.nid AS nid,
   node_data_field_art_subsec_head.field_art_subsec_head_value AS node_data_field_art_subsec_head_field_art_subsec_head_value,
   node.type AS node_type,
   node.vid AS node_vid,
   node_revisions.body AS node_revisions_body,
   node_revisions.format AS node_revisions_format,
  node_data_field_art_subsec_head.field_art_sec_head_value AS node_data_field_art_subsec_head_field_art_sec_head_value
 FROM node node 
 LEFT JOIN content_field_art_bk_code node_data_field_art_bk_code ON node.vid = node_data_field_art_bk_code.vid
 LEFT JOIN content_field_art_sec_no node_data_field_art_sec_no ON node.vid = node_data_field_art_sec_no.vid
 LEFT JOIN content_field_art_no node_data_field_art_no ON node.vid = node_data_field_art_no.vid
 LEFT JOIN content_type_article_content node_data_field_art_subsec_head ON node.vid = node_data_field_art_subsec_head.vid
 LEFT JOIN node_revisions node_revisions ON node.vid = node_revisions.vid
 WHERE (node.type in ('article_content')) AND (node_data_field_art_bk_code.field_art_bk_code_value = '".arg(4)."') AND (node_data_field_art_sec_no.field_art_sec_no_value = ".arg(5).") AND (node_data_field_art_no.field_art_no_value = ".arg(7).") limit 1");
 
 $result44 = db_fetch_object($query);
 $sec_head =  $result44->node_data_field_art_subsec_head_field_art_sec_head_value;

$titl= "<br/><h3>".arg(8)." - ".$sec_head."</h3>";
	 }
	 if(arg(2)=="timeline" && arg(3)=="pdf")
	 {
	 $titl= "<br/><h3>Timeline</h3><br/>";
	 }
	 $logo='<br/><br/><img src="/sites/all/themes/alim/images/alim-logo-boot.png" width="188"  border="0" ><br/>';
	  if(arg(3)=="compare")
	  {
	  $book="Compare Translation"."-Surah ".arg(4)."-Ayah ".arg(5);
	  $link="http://alim.org/library/quran/ayah/compare/".arg(4)."/".arg(5);
	  }
	   if(arg(5)=="ASD")
	   {
	  $book="Asad Translation"."-Surah ".arg(4);
	  $link="http://alim.org/library/quran/surah/english/".arg(4)."/ASD";
	  }
	   if(arg(5)=="MAL")
	   {
	  $book="Malik Translation"."-Surah ".arg(4);
	  $link="http://alim.org/library/quran/surah/english/".arg(4)."/MAL";
	  }
	  if(arg(5)=="YAT")
	  {
	  $book="YusufAli Translation"."-Surah ".arg(4);
	  $link="http://alim.org/library/quran/surah/english/".arg(4)."/YAT";
	  }
	  if(arg(5)=="PIK")
	  {
	  $book="Pickthall Translation"."-Surah ".arg(4);
	  $link="http://alim.org/library/quran/surah/english/".arg(4)."/PIK";
	  }
	   if(arg(5)=="TLT")
	   {
	  $book="Qura'n Transliteration"."-Surah ".arg(4);
	  $link="http://alim.org/library/quran/surah/english/".arg(4)."/TLT";
	  }
	   if(arg(3)=="SAD")
	   {
	  $book="Abu-Dawood Hadith-".arg(4);
	 // $link="http://alim.org/library/hadith/SAD/".arg(4);
	  }
	  if(arg(3)=="AMH")
	  {
	  $book="Al-Muwatta Hadith-".arg(4).".".arg(5);
	 //  $link="http://alim.org/library/hadith/AMH/".arg(4)."/".arg(5);
	  }
	  if(arg(2)=="NWH")
	  {
	  $book="Al-Nawawi Hadith-".arg(3).".".arg(4);
	 // $link="http://alim.org/library/hadith/NWH/".arg(3)."/".arg(4);
	  }
	  if(arg(3)=="HDQ")
	  {
	  $book="Al-Qudsi Hadith-".arg(4);
	  //$link="http://alim.org/library/hadith/HDQ/".arg(4);
	  }
	  if(arg(3)=="TIR")
	  {
	  $book="Al-Tirmidhi Hadith-".arg(4);
	  // $link="http://alim.org/library/hadith/TIR/".arg(4);
	  }
	  if(arg(3)=="FQS")
	  {
	  $book="Fiqh-us-Sunnah Hadith-".arg(4).".".arg(5);
	  $link="http://alim.org/library/hadith/fiq/FQS/".arg(4)."/".arg(5);
	  }
	  if(arg(3)=="SHB")
	  {
	  $book="Sahih Al-Bukhari Hadith-".arg(4).".".arg(5);
	 // $link="http://alim.org/library/hadith/SHB/".arg(4)."/".arg(5);
	  }
	    if(arg(3)=="SHM")
		{
	  $book="Sahih Muslim Hadith-".arg(4);
	  //$link="http://alim.org/library/hadith/SHM/".arg(4);
	 
	  }
	    if(arg(2)=="timeline")
		{
	  $book="Timeline History-".arg(4);
	   $link="http://alim.org/library/history/timeline/".arg(4);
	  }
	    if(arg(4)=="KAB")
		{
	  $book="Khalifa Abu Bakr History";
	  // $link="http://alim.org/library/biography/khalifa/content/KAB/".arg(5)."/".arg(7);
	  }
	   if(arg(4)=="KUM")
	   {
	  $book="Umar bin al-Khattab History";
	 // $link="http://alim.org/library/biography/khalifa/content/KUM/".arg(5)."/".arg(7);
	  }
	   if(arg(4)=="KUT")
	   {
	  $book="Uthman History";
	  //  $link="http://alim.org/library/biography/khalifa/content/KUT/".arg(5)."/".arg(7);
	  }
	   if(arg(4)=="KAL")
	   {
	  $book="Ali bin Abu Talib History";
	 //  $link="http://alim.org/library/biography/khalifa/content/KAL/".arg(5)."/".arg(7);
	  }
	  if(arg(2)=="stories")
	  {
	  $book= "".arg(8)." Stories";
	   $link="http://alim.org/library/biography/stories/content/SOP/".arg(5)."/".arg(7);
	  }
	   if(arg(2)=="prophet")
	   {
	  $book= "Prophet Muhammad's (SAW)";
	 //   $link="http://alim.org/library/hadith/prophet";
	  }
	   
	 if($row=="Alim.org")
	 {
	 if(arg(5)=="ASD" || arg(5)=="YAT"){
	 $argnull="";
	 $coment= views_embed_view('ayahelaboration_bottom', 'block_2', array(arg(4),arg(5),$argnull));
	 $view = views_get_view('ayahelaboration_bottom');
	 $coment = $view->execute_display('block_2', array(arg(4),arg(5)));
	 $coment=$coment['content'];
	 }
	 if(arg(3)=="compare") 
	 {
	 $view = views_get_view('ayah_elaboration_author');
	 $coment = $view->execute_display('block_1', array(arg(4),arg(5)));
	 $coment=$coment['content'];

	 }
	 if($coment)
	 $coment = "<h4 style=color:'red'>Author Comments</h4>".$coment;


	 $titl="";
	 $logo="";
	 if($link)
	 $link_page = '<br/><a href = "'.$link.'">View Page</a> ';
	 $row= "<br/><br/>".$coment."<span style='color:#58595B;'>".$link_page."<i><br/><br/>Source : Alim.org-".$book."</i></span><br/>";
	 }
	  
     $content = $logo.$titl.$row;

     // $content = $titl.$row;
    }
    else {
      // We got bad data. So return.
      return;
    }
    
    
    if (!empty($view->field[$key]->options['exclude'])) {
      return '';
    }
    
    // Apply the hyphenation patterns to the content:
/*    if (!isset($options['text']['hyphenate']) && is_object($view) && is_object($view->display_handler)) {
      $options['text']['hyphenate'] = $view->display_handler->get_option('default_text_hyphenate');
    }
*/    
    
    
    if (isset($options['text']['hyphenate']) && $options['text']['hyphenate'] != 'none') {
      $patternFile = $options['text']['hyphenate'];
      if ($options['text']['hyphenate'] == 'auto' && is_object($row)) {
        $nodeLanguage = $row->node_language;
        foreach (self::getAvailableHyphenatePatterns() as $file => $pattern) {
          if (stristr($pattern, $nodeLanguage) !== FALSE) {
            $patternFile = $file;
            break;
          }
        }
      }
      
      $patternFile = views_pdf_get_library('tcpdf') . '/hyphenate_patterns/' . $patternFile;
      
      if (file_exists($patternFile)) {
        $hyphen_patterns = $this->getHyphenPatternsFromTEX($patternFile);
        
        // Bugfix if you like to print some html code to the PDF, we
        // need to prevent the replacement of this tags.
        $content = str_replace('&gt;', '&amp;gt;', $content);
        $content = str_replace('&lt;', '&amp;lt;', $content);
        $content = $this->hyphenateText($content, $hyphen_patterns);
        
      }  
    }  
    
    // Set css variable
    if (is_object($view) && is_object($view->display_handler)) {
      $css_file = $view->display_handler->get_option('css_file');
    }
    
    $font_size = empty($options['text']['font_size']) ? $this->defaultFontSize : $options['text']['font_size'] ;
    $font_family = ($options['text']['font_family'] == 'default' || empty($options['text']['font_family'])) ? $this->defaultFontFamily : $options['text']['font_family'];
    $font_style = is_array($options['text']['font_style']) ? $options['text']['font_style'] : $this->defaultFontStyle;
    $textColor = !empty($options['text']['color']) ? $this->parseColor($options['text']['color']) : $this->parseColor($this->defaultFontColor);
    
    
    $w = $options['position']['width'];
    $h = $options['position']['height'];
    $border = 0;
    $align = isset($options['text']['align']) ? $options['text']['align'] : $this->defaultTextAlign;
    $fill = 0;
    $ln = 1;
    $reseth = true;
    $stretch = 0;
    $ishtml = isset($options['render']['is_html']) ? $options['render']['is_html'] : 1;
    $stripHTML = !$ishtml;
    $autopadding = true;
    $maxh = 0;
    $valign = 'T';
    $fitcell = false;
    
    // Run eval before
    eval($options['render']['eval_before']);

    // Add css if there is a css file set and stripHTML is not 
    // active
    if (!empty($css_file) && is_string($css_file) && !$stripHTML && $ishtml && !empty($content)) {
      $content = '<link type="text/css" rel="stylesheet" media="all" href="' . $css_file . '" />' . "\n" . $content;
    }

    
    
    // Set Text Color
    $this->SetTextColorArray($textColor);
    
    // Set font
    $this->SetFont($font_family, implode('', $font_style), $font_size);
    
    // Save the last page before starting writing, this
    // is needed to dected if we write over a page. Then we need
    // to reset the y coordinate for the 'last_writing' position option.
    $this->lastWritingPage = $this->getPage();
    
    if ($stripHTML) {
      $content = strip_tags($content);
    }
    
    
                
    // Write the content of a field to the pdf file:
	$searchhtmlsup = array("<span class='fn'>", "</span>");
	$replaceoriginalsup = array('<sup style="color:red"><b>', '</b></sup>');
	$content = str_replace($searchhtmlsup, $replaceoriginalsup, $content);
/*	if($link)
	{
	$content =$content."Link to Page";
	$searchhtmlsup = array("Link to Page");
	$replaceoriginalsup = array('<br/><a href = "'.$link.'">Link to Page</a> ');
	$content = str_replace($searchhtmlsup, $replaceoriginalsup, $content);

	}
*/    $this->MultiCell($w, $h, $content, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    
    // Reset font to default
    $this->SetFont($this->defaultFontFamily, implode('', $this->defaultFontStyle), $this->defaultFontSize);
    
    // Run eval after
    eval($options['render']['eval_after']);
    
    // Write Coordinates of element
    $this->elements[$key] = array(
      'x' => $x,
      'y' => $y,
      'width' => empty($w) ? ($pageDim['wk'] - $this->rMargin-$x) : $w,
      'height' => $this->y - $y,
      'page' => $this->lastWritingPage,
    );
    
    $this->lastWritingElement = $key;
  }
  
  
  /**
   * This method draws a table on the PDF.
   */
  public function drawTable(&$view, $options) {
    
    $rows = $view->result;
    $columns = $view->field;
    $pageDim = $this->getPageDimensions();
    
    // Set draw point to the indicated position:
    if (empty($options['position']['x'])) {
      $options['position']['x'] = 10;
    }
        
    if (empty($options['position']['y'])) {
      $options['position']['y'] = 0;
    }
    
    if (isset($options['position']['last_writing_position']) && $options['position']['last_writing_position']) {
      $y = $options['position']['y'] + $this->y;
      $x = $options['position']['x'] + $this->x;    
    }
    else {
      $y = $options['position']['y'];
      $x = $options['position']['x'];    
    }
    
    if (isset($options['position']['width']) && !empty($options['position']['width'])) {
      $width = $options['position']['width'];
    }
    else {
      $width = $pageDim['wk'] - $this->rMargin - $x;
    }
    
    $sumWidth = 0;
    $numberOfColumnsWithoutWidth = 0;
    
    // Set the definitiv width of a column
    foreach ($columns as $id => $columnName) {/*
	print $columnName;
      if (isset($options['info'][$id]['position']['width']) && !empty($options['info'][$id]['position']['width'])){
        $sumWidth += $options['info'][$id]['position']['width'];
      }
      else {
        $numberOfColumnsWithoutWidth++;
      }
    */}
    if ($numberOfColumnsWithoutWidth > 0) {
      $defaultColumnWidth = ($width - $sumWidth) / $numberOfColumnsWithoutWidth;
    }
    else {
      $defaultColumnWidth = 0;
    }
    
    // Print header:
    
    $rowX = $x;
    $page = $this->getPage();
    if ($page == 0) {
      $this->addPage();
      $page = $this->getPage();
    }
    
    
    foreach ($columns as $id => $column) {
      
      if (!empty($column->options['exclude'])) {
        continue;
      }

      $headerOptions = $options['info'][$id]['header_style'];
      
      if (isset($options['info'][$id]['position']['width']) && !empty($options['info'][$id]['position']['width'])){
        $headerOptions['position']['width'] = $options['info'][$id]['position']['width'];
      }
      else {
        $headerOptions['position']['width'] = $defaultColumnWidth;
      }
      $headerOptions['position']['object'] = 'last_position_without_reset';
      $this->SetY($y);
      $this->SetX($x);
      $this->setPage($page);
    
      $this->renderRow($x, $y, $column->options['label'], $headerOptions);
      $x += $headerOptions['position']['width'];
    }
    
    $rowY = $this->y;
    
    if (!isset($options['position']['row_height']) || empty($options['position']['row_height'])) {
      $options['position']['row_height'] = 10;
    }
    
    
    foreach ($rows as $row) {
      $x = $rowX;
 
       // Get the page dimensions
      $pageDim = $this->getPageDimensions();
            
      if (($rowY + $this->bMargin + $options['position']['row_height']) > $pageDim['hk']) {
        $rowY = $this->tMargin;
        $this->addPage();
      }
      
      if ($this->lastWritingPage != $this->getPage()) {
        $rowY = $this->y; // $rowY - $pageDim['hk']
      }
      
      $y = $rowY;
      $page = $this->getPage();
      foreach ($columns as $id => $column) {
      
        if (!empty($column->options['exclude']) && is_object($view->field[$id])) {
          // Render the element, but dont print the output. This
          // is required to allow the use of tokens in other fields.
          $view->field[$id]->theme($row);
          continue;
        }

        $bodyOptions = $options['info'][$id]['body_style'];
      
        if (isset($options['info'][$id]['position']['width']) && !empty($options['info'][$id]['position']['width'])){
          $bodyOptions['position']['width'] = $options['info'][$id]['position']['width'];
        }
        else {
          $bodyOptions['position']['width'] = $defaultColumnWidth;
        }
        $bodyOptions['position']['object'] = 'last_position';
        
        $this->setPage($page);
        $this->SetY($y);
        $this->SetX($x);
        
        $bodyOptions['position']['height'] = 0;
                
        $this->renderRow($x, $y, $row, $bodyOptions, $view, $id);
        
        $x += $bodyOptions['position']['width'];
        
        // If the cell is writting over the row, we need to adjust the 
        // row y position.
        if (($rowY + $options['position']['row_height']) < $this->y) {
          $rowY = $this->y - $options['position']['row_height'];
        }
 
      }
      
      $rowY += $options['position']['row_height'];
      
    }
    
    $this->SetY($rowY + $options['position']['row_height']);
    $this->renderRow($x, $rowY, "Alim.org",$rowY + $options['position']['row_height']);
    
  }
  
  
  /**
   * This method adds a existing PDF document to the current document. If 
   * the file does not exists this method will return 0. In all other cases 
   * it will returns the number of the added pages.
   *
   * @param $path string Path to the file
   * @return integer Number of added pages
   */
  public function addPdfDocument($path) {
    if (empty($path) || !file_exists($path)) {
      return 0;
    }
    
    $numberOfPages = $this->setSourceFile($path);
    for ($i = 1; $i <= $numberOfPages; $i++) {
      
      $dim = $this->getTemplateSize($i);
      $format[0] = $dim['w'];
      $format[1] = $dim['h'];
      
      if ($dim['w'] > $dim['h'])
      {
        $orientation = 'L';
      }
      else
      {
        $orientation = 'P';
      }
      $this->setPageFormat($format, $orientation);
      parent::addPage();
      
      // Ensure that all new content is printed to a new page
      $this->y = 0;

      $page = $this->importPage($i);
      $this->useTemplate($page,0,0);
      $this->addNewPageBeforeNextContent = true;    
    }
        
    return $numberOfPages;
    
  }
  
  /**
   * This method resets the page number. This is useful if you want to start
   * the numbering by zero.
   */
  public function resetRowPageNumber() {
    $this->rowContentPageNumber = 0;
  }
  
  /**
   * This method adds a new page to the PDF.
   */
  public function addPage($path = NULL, $reset = false, $numbering = 'main') {
    
    $this->mainContentPageNumber++;
    $this->rowContentPageNumber++;
    
    // Reset without any template
    if ((empty($path) || !file_exists($path)) && $reset == true) {
      parent::addPage();
      $this->setPageFormat($this->defaultFormat, $this->defaultOrientation);
      return;
    }
    
    $files = $this->defaultPageTemplateFiles;
    
    // Reset with new template
    if ($reset) {
      $files = array();
    }
    
    if ($path != NULL) {
      $files[] = array('path' => $path, 'numbering' => $numbering);
    }
    $format = false;
    foreach ($files as $file) {
      if (!empty($file['path']) && file_exists($file['path'])) {
        $path = realpath($file['path']);

        $numberOfPages = $this->setSourceFile($path);
        if ($file['numbering'] == 'row')  {
          $index = min($this->rowContentPageNumber, $numberOfPages);
        }
        else {
          $index = min($this->mainContentPageNumber, $numberOfPages);
        }
        
    
        $page = $this->importPage($index);
    
        // ajust the page format (only for the first template)
        if ($format == false) {
          
          $dim = $this->getTemplateSize($index);
          $format[0] = $dim['w'];
          $format[1] = $dim['h'];
          //$this->setPageFormat($format);
          if ($dim['w'] > $dim['h']) {
            $orientation = 'L';
          }
          else {
            $orientation = 'P';
          }
          $this->setPageFormat($format, $orientation);
          parent::addPage();
        }
        
        // Apply the template
        $this->useTemplate($page,0,0);
      }
    }
    
    // if all paths were empty, ensure that at least the page is added
    if ($format == false) {
      parent::addPage();
      $this->setPageFormat($this->defaultFormat, $this->defaultOrientation);
    }
    
    // When a new page is added, we copy the header / footer data from the prvevious page.
    // this ensures that we get always the data, independent on a page break
    // inside a record.
    if (isset($this->headerFooterData[($this->getPage() - 1)])) {
    //  $this->headerFooterData[$this->getPage()] = $this->headerFooterData[($this->getPage() - 1)];
    }  
    
  }
  
  /**
   * Sets the current header and footer of the page.
   */
  public function setHeaderFooter($record, $options, $view) {
    //if ($this->getPage() > 0 && !isset($this->headerFooterData[$this->getPage()])) {
      $this->headerFooterData[$this->getPage()] = $record;
    //}
    $this->headerFooterOptions = $options;
    $this->view =& $view;
  }
  
  /**
   * Close the document. This is called automatically by 
   * TCPDF::Output().
   */
  public function Close() {
    // Print the Header & Footer 
    for ($page = 1; $page <= $this->getNumPages(); $page++) {
      $this->setPage($page);  
      
      if (isset($this->headerFooterData[$page])) {
        $record = $this->headerFooterData[$page];
        if (is_array($this->headerFooterOptions['formats'])) {
          foreach ($this->headerFooterOptions['formats'] as $id => $options) {
            if ($options['position']['object'] == 'header_footer') {
              $fieldOptions = $options;
              $fieldOptions['position']['object'] = 'page';
              $this->InFooter = true;
          
              // backup margins
              $ml = $this->lMargin;
              $mr = $this->rMargin;
              $mt = $this->tMargin;
              $this->SetMargins(0,0,0);
          
              $this->drawContent($record, $fieldOptions, $this->view, $id);
              $this->InFooter = false;
          
              // restore margins
              $this->SetMargins($ml,$mt,$mr);
            }
          }
        }
      }
    }
    
    // call parent:
    parent::Close();  
    
  }
  
  /**
   * This method returns a list of current uploaded files.
   */
  public static function getAvailableTemplates() {
    if(self::$templateList != NULL) {
      return self::$templateList;
    }
    
    $files_path = file_directory_path();
    $template_dir = variable_get('views_pdf_template_path','views_pdf_templates');
    $dir = $files_path.'/'.$template_dir;
    $templatesFiles = file_scan_directory($dir, '.pdf', array('.', '..', 'CVS'), 0, FALSE, 'filename', 0, 1);
    
    $templates = array();
    
    foreach ($templatesFiles as $file) {
      $templates[$file->name] = $file->name;
    }
    
    self::$templateList = $templates;
    
    return $templates;
  
  }
  
  /**
   * This method returns the path to a specific template.
   */
  public static function getTemplatePath($template, $row = null, $view = null) {
    if (empty($template)) {
      return '';
    }
    
    if ($row != null && $view != null && !preg_match('/\.pdf/', $template)) {
      return $view->field[$template]->theme($row);
    }
  
    $files_path = file_directory_path();
    $template_dir = variable_get('views_pdf_template_path','views_pdf_templates');
    $dir = $files_path.'/'.$template_dir;
    
    return $dir . '/' . $template.'.pdf';
    
  }

  /**
   * This method returns a list of available fonts. 
   */
  public static function getAvailableFonts() {
    if (self::$fontList != NULL) {
      return self::$fontList;
    }
    
    // Get all pdf files with the font list: K_PATH_FONTS
    $fonts = file_scan_directory(K_PATH_FONTS, '.php', array('.', '..', 'CVS'), 0, FALSE, 'filename', 0, 1);
    $cache = cache_get('views_pdf_cached_fonts');
    $cached_font_mapping = $cache->data;
    
    if (is_array($cached_font_mapping) ) {
      $font_mapping = array_merge(self::$defaultFontList, $cached_font_mapping);
    }
    else {
      $font_mapping = self::$defaultFontList;
    }
    
    foreach ($fonts as $font) {
      if (!isset($font_mapping[$font->name])) {
        $font_mapping[$font->name] = self::getFontNameByFileName($font->filename);
      }
    }
    
    asort($font_mapping);
    
    cache_set('views_pdf_cached_fonts', $font_mapping);
    
    // Remove all fonts without name
    foreach ($font_mapping as $key =>$font) {
      if (empty($font)) {
        unset($font_mapping[$key]);
      }
        
    }
    
    self::$fontList = $font_mapping;
    
    return $font_mapping;
  }
  
  /**
   * This method returns a cleaned up version of the font list.
   */
  public static function getAvailableFontsCleanList() {
    if (self::$fontListClean != NULL) {
      return self::$fontListClean;
    }
    
    $clean = self::getAvailableFonts();

    foreach ($clean as $key => $font) {
      
      // Unset bold, italic, italic/bold fonts
      unset($clean[ ($key.'b') ]);
      unset($clean[ ($key.'bi') ]);
      unset($clean[ ($key.'i') ]);
      
    }
    
    self::$fontListClean = $clean;
    
    return $clean;
  }
  
  /**
   * This method returns a list of hyphenation patterns, that are 
   * available.
   */
  public static function getAvailableHyphenatePatterns() {
    if (self::$hyphenatePatterns != NULL) {
      return self::$hyphenatePatterns;
    }
    
    self::$hyphenatePatterns = array();
    
    $files = file_scan_directory(views_pdf_get_library('tcpdf') . '/hyphenate_patterns', '.tex', array('.', '..', 'CVS'), 0, FALSE, 'filename', 0, 1);
    
    foreach ($files as $file) {
      self::$hyphenatePatterns[basename($file->filename)] = str_replace('hyph-', '', $file->name);
    }
    
    
    return self::$hyphenatePatterns;
  }
  
  /**
   * This method returns the name of a given font. 
   */
  protected static function getFontNameByFileName($path) {
    include $path;
    return $name;
  }
}


