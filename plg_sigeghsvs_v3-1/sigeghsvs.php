<?php
/**
cclicence
thumbs - Generate and save thumbnails - 1 = yes, 0 = no

width - Width of the thumbnail - example: 300
height - Height of the thumbnail - example: 300
gap_v - Vertical gap - example: 30
gap_h - Horizontal gap - example: 30
quality - Quality (jpg) - möglich: 1-100
quality_png - Quality (png) - possible: 1-9 (Compression level)

displayarticle - Show article name - 1 = yes, 0 = no

limit - Activate limiting - 1 = yes, 0 = no
limit_quantity - Show number of images - example: 10
noslim - Deactivate lightbox effect - 1 = yes, 0 = no
random - Random order - 1 = yes, 2 = no, ascending, 3 = no, descending, 4 = ascending by modified date, 5 = descending by modified date
root - Path to the images starting at the root folder - 1 = yes, 0 = no
ratio - Maintain aspect ratios of the images - 1 = yes, 0 = no
caption - Display captions - 1 = yes, 0 = no
count - Set count variable manually - example: 5
single - Show a single image - example: imagename.jpg
scaption - Caption in single view, only use with parameter single - example: This is my caption
single_gallery - Show gallery if parameter single is used - 1 = yes, 0 = no
salign - Align single image - example: left / center / right
connect - Connect images in other syntax calls - example: bildset
download - Show download button - 1 = yes, 0 = no
list - Display images in a list - 1 = yes, 0 = no
crop - Enable crop function - 1 = yes, 0 = no
crop_factor - Zoom level - example 50 for 50 percent (enter without %!)
thumbdetail - Choose image section for thumbnail - 0 = 1:1, 1 = top left, 2 = top right, 3 = bottom left, 4 = bottom right
watermark - Enable watermark function - 1 = yes, 0 = no
watermarkposition - Position of the watermark - 0 = centered, 1 = top left, 2 = top right, 3 = bottom left, 4 = bottom right
watermark_trans - Transparency of the watermark - 0 to 100 - 0 = opaque, 100 - completely transparent
encrypt - Encryption method - 0 = ROT13 - very weak, but fast. 1= MD5 - safe, fast. 2 = SHA1 - very safe, slower than MD5
image_info - Show image name - 1 = yes, 0 = no
image_link - Set a link - example: www.kubik-rubik.de
image_link_new - Open link in a new window - 1 = yes, 0 = no
column_quantity - Images per line - example: 3 (for 3 images in one line)
css_image - Enable CSS Image Tooltip - 1 = yes, 0 = no
css_image_half - Half the size in tooltip - 1 = yes, 0 = no
watermarkimage - Set a different watermark image (image has to be located in plugins/content/sigeghsvs/plugin_sigeghsvs) - example: watermark-new.png
calcmaxthumbsize - Calculate maximum size of all thumbnails - 1 = yes, 0 = no
fileinfo - Information from text file (captions.txt - see tab Extras) - 1 = yes, 0 = no
turbo - Activate turbo mode - 1 = yes, 0 = no
resize_images - Resize original images - 1 = yes, 0 = no
width_image - Maximum width of resized images - example: 800
height_image - Maximum height of resized images - example: 800
ratio_image - Maintain aspect ratios of the original images - 1 = yes, 0 = no
images_new - Overwrite resized images - 1 = yes, 0 = no
*/
defined('_JEXEC') or die('Restricted access');
class plgContentSigeghsvs extends JPlugin{
 protected $_absolute_path;
 protected $_live_site;
 protected $_rootfolder;
 protected $_images_dir;
 protected $_syntax_parameter;
 protected $_params;
 protected $_article_title;
 protected $_thumbnail_max_height;
 protected $_thumbnail_max_width;
 protected $_turbo_html_read_in;
 protected $_turbo_css_read_in;
	function __construct(&$subject, $config){
		$app=JFactory::getApplication();
		if($app->isClient('administrator'))return;
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_content_sigeghsvs', JPATH_ADMINISTRATOR);
		if(isset($_SESSION["sigcount"]))unset($_SESSION["sigcount"]);
		/* Ein Counter. Steuert das einmalige Einsetzen der Scripts im HEAD. */
		if(isset($_SESSION['sigcountarticles']))unset($_SESSION['sigcountarticles']);
		$this->_absolute_path=JPATH_SITE;
		$this->_live_site=JURI::base();
		if(substr($this->_live_site, -1)== "/"){
			$this->_live_site=substr($this->_live_site, 0, -1);
		}
		$this->_params=array();
	}
private function setParams(){
 $params=array('width', 'height', 'ratio', 'gap_v', 'gap_h', 'quality', 'quality_png', 'limit', 'thumbs', 'thumbs_new', 'view', 'limit_quantity', 'noslim', 'caption', 'salign', 'connect', 'download', 'list', 'crop', 'crop_factor', 'random', 'single', 'thumbdetail', 'watermark', 'encrypt', 'image_info', 'image_link', 'image_link_new', 'single_gallery', 'column_quantity', 'css_image', 'css_image_half', 'watermarkposition', 'watermarkimage', 'watermark_new', 'root', 'js', 'calcmaxthumbsize', 'fileinfo', 'fileinfo_part2', 'turbo', 'resize_images', 'width_image', 'height_image', 'ratio_image', 'images_new', 'scaption', 'cclicence');
 foreach($params as $value){
  $this->_params[$value]=$this->getParams($value);
 }
 $count=$this->getParams('count', 1);
 if(!empty($count)){
  $_SESSION["sigcount"]=$count;
 }
}
private function getParams($param, $syntax_only=0){
 if($syntax_only== 1){
  if(array_key_exists($param, $this->_syntax_parameter) AND $this->_syntax_parameter[$param] != ""){
   return $this->_syntax_parameter[$param];
  }
 }else{
  if(array_key_exists($param, $this->_syntax_parameter) AND $this->_syntax_parameter[$param] != ""){
   return $this->_syntax_parameter[$param];
  }else{
   return $this->params->get($param);
  }
 }
}
function onContentPrepare($context, &$article, &$params, $limitstart){
	if(!preg_match("@{sigeghsvs}(.*){/sigeghsvs}@Us", $article->text)){
		return;
	}
	$imagesDir=$this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/';
	#GD-Support?
 if(function_exists("gd_info")){
  $gdinfo=gd_info();
  $gdsupport=array();
  $version=intval(preg_replace('/[[:alpha:][:space:]()]+/', '', $gdinfo['GD Version']));
  if($version != 2){
   $gdsupport[]='<div class="message">GD Bibliothek nicht vorhanden</div>';
  }
  if(substr(phpversion(), 0, 3) < 5.3){
   if(!$gdinfo['JPG Support']){
    $gdsupport[]='<div class="message">GD JPG Bibliothek nicht vorhanden</div>';
   }
  }else{
   if(!$gdinfo['JPEG Support']){
    $gdsupport[]='<div class="message">GD JPG Bibliothek nicht vorhanden</div>';
   }
  }
  if(!$gdinfo['GIF Create Support']){
   $gdsupport[]='<div class="message">GD GIF Bibliothek nicht vorhanden</div>';
  }
  if(!$gdinfo['PNG Support']){
   $gdsupport[]='<div class="message">GD PNG Bibliothek nicht vorhanden</div>';
  }
  if(count($gdsupport)){
   foreach($gdsupport as $k=> $v)echo $v;
  }
 }
 #ENDE - GD Support?
 if(!isset($_SESSION['sigcountarticles'])){
  $_SESSION['sigcountarticles']=-1;
 }
 if(preg_match_all("@{sigeghsvs}(.*){/sigeghsvs}@Us", $article->text, $matches, PREG_PATTERN_ORDER) > 0){
  $_SESSION['sigcountarticles']++;
  if(!isset($_SESSION["sigcount"])){
   $_SESSION["sigcount"]=-1;
  }
  $this->_params['lang']=JFactory::getLanguage()->getTag();
		foreach($matches[0] as $match){
			$_SESSION["sigcount"]++;
			$sige_code=preg_replace("@{.+?}@", "", $match);
			$sige_array=explode(",", $sige_code);
			$this->_images_dir=$sige_array[0];
			unset($this->_syntax_parameter);
			$this->_syntax_parameter=array();
			#Falls weitere Parameter im Platzhalter:
			if(count($sige_array) >= 2){
				for($i=1; $i < count($sige_array); $i++){
					$parameter_temp=explode("=", $sige_array[$i]);
					if(count($parameter_temp) >= 2){
						$this->_syntax_parameter[strtolower(trim($parameter_temp[0]))]=trim($parameter_temp[1]);
					}
				}
			}
   unset($sige_array);
   $this->setParams();
   if(!$this->_params['root']){
    $this->_rootfolder='/images/';
   }else{
    $this->_rootfolder='/';
   }
			#Turbo-Mode?
   $this->_turbo_html_read_in=false;
   $this->_turbo_css_read_in=false;
   if($this->_params['turbo']){
				if($this->_params['turbo']== 'new'){
					$this->_turbo_html_read_in=true;
					$this->_turbo_css_read_in=true;
				}else{
					if(!file_exists($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/sige_turbo_html-'.$this->_params['lang'].'.txt')){
						$this->_turbo_html_read_in=true;
					}
     if(!file_exists($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/sige_turbo_css-'.$this->_params['lang'].'.txt')){
				  $this->_turbo_css_read_in=true;
     }
    }
   }
			#ENDE - Turbo-Mode?
   if(!$this->_params['turbo'] OR ($this->_params['turbo'] AND $this->_turbo_html_read_in)){
    unset($images);
    $noimage=0;
				#Bilder einlesen nach $images:
    if($dh=@opendir($this->_absolute_path.$this->_rootfolder.$this->_images_dir)){
     while(($f=readdir($dh)) !== false){
						if(substr(strtolower($f), -3)=='jpg'
						   OR substr(strtolower($f), -3)=='gif'
									OR substr(strtolower($f), -3)=='png'){
							$images[]=array('filename'=> $f);
							$noimage++;
						}
     }
     closedir($dh);
    }
    #ENDE - Bilder einlesen nach $images
    if($noimage){#Falls Bilder gefunden
     if(!file_exists($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/index.html')){
						file_put_contents($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/index.html', "");
				  file_put_contents($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/.htaccess', "#Auto generated by sigeghsvs\ndeny from all");
     }
     #Gibt einen Parameter auch den Artikeltitel anzeigen zu lassen:
     $jview=JRequest::getWord('view');
     if($jview != 'featured' AND isset($article->title)){
				  $this->_article_title=preg_replace("@\"@", "'", $article->title);
     }
     #Bilder $images sortieren
     if($this->_params['random']== 1){
				  shuffle($images);
     }elseif($this->_params['random']== 2){
				  sort($images);
     }elseif($this->_params['random']== 3){
				  rsort($images);
     }elseif($this->_params['random']== 4 OR $this->_params['random']== 5){
				  for($a=0; $a < count($images); $a++){
       $images[$a]['timestamp']=filemtime($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/'.$images[$a]['filename']);
						}
						if($this->_params['random']== 4){
							usort($images, array($this, 'timeasc'));
						}elseif($this->_params['random']== 5){
							usort($images, array($this, 'timedesc'));
						}
					}
     #ENDE - Bilder $images sortieren
     $noimage_rest=0;
     $single_yes=false;
					#Falls Para single=bildname.jpg: das Bild nach $images[0].
     if($this->_params['single']){
				  $count=count($images);
      if($images[0]['filename']==$this->_params['single']){
       if($this->_params['single_gallery']){
        $noimage_rest=$noimage;
        $this->_params['limit_quantity']=1;
       }
       $noimage=1;
       $single_yes=true;
      }else{
       for($a=1; $a < $noimage; $a++){
        if($images[$a]['filename']== $this->_params['single']){
         if($this->_params['single_gallery']){
          $noimage_rest=$noimage;
          $this->_params['limit_quantity']=1;
         }
         $noimage=1;
         $images[$count]=$images[0];
         $images[0]=array('filename'=> $this->_params['single']);
         unset($images[$a]);
         $images[$a]=$images[$count];
         unset($images[$count]);
         $single_yes=true;
         break;
        }
       }
      }
     }
     #ENDE - Falls Para single=bildname.jpg: das Bild nach $images[0].
					#Captions.txt?
					$file_info=false;
					if($this->_params['encrypt']==50){
						$file_info=$this->getFileInfo();
						if(!empty($file_info)){
							foreach($images as $idx=>$image){
								foreach($file_info as $arr){
									if($arr[0]==$image['filename']){
										$fileName=array();
										if(isset($arr[1]) && trim($arr[1])){
											$fileName[]=trim(preg_replace('/\s\s+/', ' ', $arr[1]));
										}
										if(isset($arr[2]) && trim($arr[2])){
											$fileName[]=trim(preg_replace('/\s\s+/', ' ', $arr[2]));
										}
										if(count($fileName)){
											#$images[$idx]['filenameghsvs']=JApplication::stringURLSafe(join('_', $fileName));
											$images[$idx]['filenameghsvsnormal']=join('_', $fileName);
										}
										if(isset($arr[3]) && trim($arr[3])){
											$fileName[]=trim(preg_replace('/\s\s+/', ' ', $arr[3]));
										}
										if(count($fileName)){
											$images[$idx]['filenameghsvs']=JApplication::stringURLSafe(join('_', $fileName));
											#$images[$idx]['filenameghsvsnormal']=join('_', $fileName);
										}
										#Angaben zu Watermark in captions.txt
										if(isset($arr[4])){
											$WM=explode(',', $arr[4]);
											foreach($WM as $part){
												$parts=explode('=', $part);
												if(isset($parts[0]) && isset($parts[1])){
													$parts[0]=trim($parts[0]);
													$parts[1]=trim($parts[1]);
													if($parts[0] && $parts[1]){
														$images[$idx][$parts[0]]=$parts[1];
													}
												}
											}
										}
										break;
								 }
								}
							}
						}
						$this->_params['encrypt']=1;
					}
					#ENDE - if($this->_params['encrypt']==50)
     if($this->_params['fileinfo'] && !$file_info){
				  $file_info=$this->getFileInfo();
     }elseif(!$this->_params['fileinfo']){
						$file_info=false;
					}
					#ENDE - Captions.txt?
     if($this->_params['calcmaxthumbsize']){
				  $this->calculateMaxThumbnailSize($images);
     }else{
				  $this->_thumbnail_max_height=$this->_params['height'];
				  $this->_thumbnail_max_width=$this->_params['width'];
     }
					#CSS basteln
     $sige_css='';
     if($this->_params['caption']){
				  $caption_height=20;
     }else{
				  $caption_height=0;
     }
     if($this->_params['salign']){
				  if($this->_params['salign']== 'left'){
							$sige_css .= ".sige_cont_".$_SESSION["sigcount"]." {width:".($this->_thumbnail_max_width + $this->_params['gap_h'])."px;height:".($this->_thumbnail_max_height + $this->_params['gap_v'] + $caption_height)."px;float:left;display:inline-block;}\n";
				  }elseif($this->_params['salign']== 'right'){
       $sige_css .= ".sige_cont_".$_SESSION["sigcount"]." {width:".($this->_thumbnail_max_width + $this->_params['gap_h'])."px;height:".($this->_thumbnail_max_height + $this->_params['gap_v'] + $caption_height)."px;float:right;display:inline-block;}\n";
				  }elseif($this->_params['salign']== 'center'){
       $sige_css .= ".sige_cont_".$_SESSION["sigcount"]." {width:".($this->_thumbnail_max_width + $this->_params['gap_h'])."px;height:".($this->_thumbnail_max_height + $this->_params['gap_v'] + $caption_height)."px;display:inline-block;}\n";
				  }
     }else{
				  $sige_css .= ".sige_cont_".$_SESSION["sigcount"]." {width:".($this->_thumbnail_max_width + $this->_params['gap_h'])."px;height:".($this->_thumbnail_max_height + $this->_params['gap_v'] + $caption_height)."px;float:left;display:inline-block;}\n";
     }
     $this->loadHeadData($sige_css);
					#ENDE - CSS basteln
     if($this->_params['resize_images']){
	     /* erzeugt resizedImages, in dem die verkleinerten Bilder mit Originalnamen landen */
      $this->resizeImages($images);
     }
					/* erzeugt aus den geresized Bildern Wasserzeichenbilder im Ordner wm/ mit NEUEM Namen */
     if($this->_params['watermark']){
      $this->watermark($images, $single_yes);
     }
     if($this->_params['limit'] AND (!$this->_params['single'] OR !$this->_params['single_gallery'])){
				  $noimage_rest=$noimage;
				  if($noimage > $this->_params['limit_quantity']){
						 $noimage=$this->_params['limit_quantity'];
      }
     }
					if($this->_params['thumbs']&&!$this->_params['list']){
						$this->thumbnails($images, $noimage);
					}

					$html='';
					if($this->_params['cclicence']){
						$cc=$this->_params['cclicence'];
						$img=JText::_('PLG_SIGEGHSVS_LICENCE_IMG_'.$cc);
						$lnk=JText::_('PLG_SIGEGHSVS_LICENCE_LINK_'.$cc);
						$des=JText::_('PLG_SIGEGHSVS_LICENCE_DESC_'.$cc);
			   #Den override.ini-Platzhalter ermittelsn:
			   $html.='<p class="cclicence">'.JText::sprintf('PLG_SIGEGHSVS_LICENCE_TEXT', $img, $lnk, $des).'</p>';
			  }
					if($this->_params['single'] AND $single_yes){
						$html .= '<ul class="sige_single">';
					}elseif(!$this->_params['list']){
						$html .= '<ul class="sige">';
					}
					if($this->_params['list']){
						$html .= '<ul>';
					}
					for($a=0; $a < $noimage; $a++){
						if(!empty($images[$a]['filenameghsvs'])){
							$filenameghsvs=$images[$a]['filenameghsvs'];
						}else{
							$filenameghsvs=false;
						}
						$html .= $this->htmlImage($images[$a]['filename'], $html, 0, $file_info, $a, $filenameghsvs);
					}
					if($this->_params['list']){
						$html .= '</ul>';
					}else{
						$html .= "</ul>\n<span class=\"sige_clr\"></span>";
					}
					if(!empty($noimage_rest) AND !$this->_params['image_link']){
						for($a=$this->_params['limit_quantity']; $a < $noimage_rest; $a++){
							if(!empty($images[$a]['filenameghsvs'])){
								$filenameghsvs=$images[$a]['filenameghsvs'];
							}else{
								$filenameghsvs=false;
							}
							$html .= $this->htmlImage($images[$a]['filename'], $html, 1, $file_info, $a, $filenameghsvs);
						}
					}
    }else{#wenn $noimage 0
     $html='<strong>'.JText::_('NOIMAGES').'</strong><br /><br />'.JText::_('NOIMAGESDEBUG').' '.$this->_live_site.$this->_rootfolder.$this->_images_dir;
    }

if($this->_turbo_html_read_in){
file_put_contents($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/sige_turbo_html-'.$this->_params['lang'].'.txt', $html);
}
}else{
$this->loadHeadData(1);

$html=file_get_contents($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/sige_turbo_html-'.$this->_params['lang'].'.txt');
}

$article->text=preg_replace("@(<p>)?{sigeghsvs}".$sige_code."{/sigeghsvs}(</p>)?@s", $html, $article->text);
}

  $this->loadHeadData();
}
}
private function timeasc($a, $b){
return strcmp($a["timestamp"], $b["timestamp"]);
}

private function timedesc($a, $b){
return strcmp($b["timestamp"], $a["timestamp"]);
}
private function encrypt($imagename){
	if($this->_params['encrypt']== 0){
		$image_hash=str_rot13($imagename);
	}elseif($this->_params['encrypt']== 1){
		$image_hash=md5($imagename);
	}elseif($this->_params['encrypt']== 2){
		$image_hash=sha1($imagename);
	}
	return $image_hash;
}
private function watermark($images, $single_yes){
 $imagesDir=$this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/';
	$wmDir=$imagesDir.'wm/';
 $resizedDir=$imagesDir.'resizedimages/';
	#Bei schwarzem Beschriftungsbalken unten muss Watermark ggf. höher:
 #$addVertGhsvs=8;
	$addVertGhsvs=0;

 if(!is_dir($wmDir)){
  mkdir($wmDir, 0755);
  file_put_contents($wmDir.'index.html', '');
  file_put_contents($wmDir.'.htaccess', "#Auto generated by sigeghsvs\nallow from all");
 }
 if(empty($this->_params['single_gallery']) && $single_yes){
  $num=1;
 }else{
  $num=count($images);
 }
 for($a=0; $a < $num; $a++){
		/* WATERMARkPOSITION
			<option value="0">CENTER</option>
			<option value="1">PLG_CONTENT_SIGE_TOPLEFT</option>
			<option value="2">PLG_CONTENT_SIGE_TOPRIGHT</option>
			<option value="3">PLG_CONTENT_SIGE_BOTTOMLEFT</option>
			<option value="4">PLG_CONTENT_SIGE_BOTTOMRIGHT</option>
		*/
		#hinterlegt in captions.txt
		if(isset($images[$a]['wmpositionghsvs'])){
			$watermarkposition=$images[$a]['wmpositionghsvs'];
		}else{
			$watermarkposition=$this->_params['watermarkposition'];
		}
		#Zusätzliches Anhängsel an Watermark-Bild
		if(isset($images[$a]['wmimgghsvs'])){
			$wmimgghsvs=$images[$a]['wmimgghsvs'];
		}else{
			$wmimgghsvs=$this->_params['watermarkimage'];
		}

  if(!empty($images[$a]['filename'])){
   $imagename=substr($images[$a]['filename'], 0, -4);
   $type=substr(strtolower($images[$a]['filename']), -3);
			if(!empty($images[$a]['filenameghsvs'])){
				$filenameghsvs=$images[$a]['filenameghsvs'].'.'.$type;

			}else{
				$filenameghsvs=false;
			}
			#Versteh ich nicht:
			if($this->_params['single_gallery'] && !$filenameghsvs){
    $image_hash=$this->encrypt($imagename).'.'.$type;
			}elseif($filenameghsvs){
				$image_hash=$filenameghsvs;
			}
			$filenamewm=$wmDir.$image_hash;
   if(!file_exists($filenamewm) || $this->_params['watermark_new']){
    if($wmimgghsvs){
     $watermarkimage=imagecreatefrompng($this->_absolute_path.'/plugins/content/sigeghsvs/plugin_sigeghsvs/'.$wmimgghsvs);
    }else{
     $watermarkimage=imagecreatefrompng($this->_absolute_path.'/plugins/content/sigeghsvs/plugin_sigeghsvs/watermark.png');
    }

				$width_wm=imagesx($watermarkimage);
				$height_wm=imagesy($watermarkimage);

				if(substr(strtolower($images[$a]['filename']), -3)== 'gif'){
					if($this->_params['resize_images']){
						$origimage=imagecreatefromgif($resizedDir.$images[$a]['filename']);
					}else{
						$origimage=imagecreatefromgif($imagesDir.$images[$a]['filename']);
					}
					$width_ori=imagesx($origimage);
					$height_ori=imagesy($origimage);

					$t_image=imagecreatetruecolor($width_ori, $height_ori);
					imagecopy($t_image, $origimage, 0, 0, 0, 0, $width_ori, $height_ori);
					$origimage=$t_image;
					if($watermarkposition== 1){
						imagecopy($origimage, $watermarkimage, 0, 0, 0, 0, $width_wm, $height_wm);
					}elseif($watermarkposition== 2){
						imagecopy($origimage, $watermarkimage, $width_ori - $width_wm, 0, 0, 0, $width_wm, $height_wm);
					}elseif($watermarkposition== 3){
						imagecopy($origimage, $watermarkimage, 0, $height_ori - $height_wm, 0, 0, $width_wm, $height_wm);
					}elseif($watermarkposition== 4){
						imagecopy($origimage, $watermarkimage, $width_ori - $width_wm, $height_ori - $height_wm, 0, 0, $width_wm, $height_wm);
					}else{
						imagecopy($origimage, $watermarkimage, ($width_ori - $width_wm) / 2, ($height_ori - $height_wm) / 2, 0, 0, $width_wm, $height_wm);
					}
					imagegif($origimage, $wmDir.$image_hash);

				}elseif(substr(strtolower($images[$a]['filename']), -3)== 'jpg'){
					if($this->_params['resize_images']){
						$origimage=imagecreatefromjpeg($resizedDir.$images[$a]['filename']);
					}else{
						$origimage=imagecreatefromjpeg($imagesDir.$images[$a]['filename']);
					}
					$width_ori=imagesx($origimage);
					$height_ori=imagesy($origimage);
					if($watermarkposition== 1){
						imagecopy($origimage, $watermarkimage, 0, 0, 0, 0, $width_wm, $height_wm);
					}elseif($watermarkposition== 2){
						imagecopy($origimage, $watermarkimage, $width_ori - $width_wm, 0, 0, 0, $width_wm, $height_wm);
					}elseif($watermarkposition== 3){
						imagecopy($origimage, $watermarkimage, 0, $height_ori - $height_wm, 0, 0, $width_wm, $height_wm);
					}elseif($watermarkposition== 4){
						imagecopy($origimage, $watermarkimage, $width_ori - $width_wm, $height_ori - $height_wm, 0, 0, $width_wm, $height_wm);
					}else{
						imagecopy($origimage, $watermarkimage, ($width_ori - $width_wm) / 2, ($height_ori - $height_wm) / 2, 0, 0, $width_wm, $height_wm);
					}
					imagejpeg($origimage, $wmDir.$image_hash, $this->_params['quality']);

				}elseif(substr(strtolower($images[$a]['filename']), -3)== 'png'){
					if($this->_params['resize_images']){
						$origimage=imagecreatefrompng($resizedDir.$images[$a]['filename']);
					}else{
						$origimage=imagecreatefrompng($imagesDir.$images[$a]['filename']);
					}
					$width_ori=imagesx($origimage);
					$height_ori=imagesy($origimage);
					if($watermarkposition== 1){//TOPLEFT
					 imagecopy($origimage, $watermarkimage, 0, 0, 0, 0, $width_wm, $height_wm);
					}elseif($watermarkposition== 2){//TOPRIGHT
					 imagecopy($origimage, $watermarkimage, $width_ori - $width_wm, 0, 0, 0, $width_wm, $height_wm);
					}elseif($watermarkposition== 3){//BOTTOMLEFT


					 imagecopy($origimage, $watermarkimage, 0, $height_ori - $height_wm - $addVertGhsvs, 0, 0, $width_wm, $height_wm);


					}elseif($watermarkposition== 4){//BOTTOMRIGHT


					 imagecopy($origimage, $watermarkimage, $width_ori - $width_wm, $height_ori - $height_wm - $addVertGhsvs, 0, 0, $width_wm, $height_wm);


					}else{//CENTER
					 imagecopy($origimage, $watermarkimage, ($width_ori - $width_wm) / 2, ($height_ori - $height_wm) / 2, 0, 0, $width_wm, $height_wm);
					}
					imagepng($origimage, $wmDir.$image_hash, $this->_params['quality_png']);


					#echo '20-2 <img src="/images/'.$this->_images_dir.'wm/'.$image_hash.'" />';



				}
				imagedestroy($origimage);
				imagedestroy($watermarkimage);
			}
  }
 }
}
private function thumbnails($images, $noimage){

 $imagesDir=$this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/';
	$thumbDir=$imagesDir.'thumbs/';
	$wmDir=$imagesDir.'wm/';

 if(!is_dir($thumbDir)){
  mkdir($thumbDir, 0755);
  file_put_contents($thumbDir.'index.html', '<html><body></body></html>');
		file_put_contents($thumbDir.'.htaccess', "#Auto generated by sigeghsvs\nallow from all");
 }

 for($a=0; $a<$noimage; $a++){
  if(!empty($images[$a]['filename'])){
   $imagename=substr($images[$a]['filename'], 0, -4);
   $type=substr(strtolower($images[$a]['filename']), -3);
			if(!empty($images[$a]['filenameghsvs'])){
				$filenameghsvs=$images[$a]['filenameghsvs'].'.'.$type;
				$image_hash=$filenameghsvs;
			}else{
				$filenameghsvs=false;
				$image_hash=$this->encrypt($imagename).'.'.$type;
			}
   if($this->_params['watermark']){
    $filenamethumb=$thumbDir.$image_hash;
   }else{
    $filenamethumb=$thumbDir.$images[$a]['filename'];
   }
   if(!file_exists($filenamethumb) || $this->_params['thumbs_new']){
    list($new_h, $new_w)=$this->calculateSize($images[$a]['filename'], 1);
    if(substr(strtolower($filenamethumb), -3)=='gif'){
					if($this->_params['watermark']){
						$origimage=imagecreatefromgif($wmDir.$image_hash);
					}else{
						$origimage=imagecreatefromgif($imagesDir.$images[$a]['filename']);
					}
					$width_ori=imagesx($origimage);
					$height_ori=imagesy($origimage);
					$thumbimage=imagecreatetruecolor($new_w, $new_h);
					if($this->_params['crop']&&($this->_params['crop_factor'] > 0 && $this->_params['crop_factor'] < 100)){
						list($crop_width, $crop_height, $x_coordinate, $y_coordinate)=$this->crop($width_ori, $height_ori);
						imagecopyresampled(
						 $thumbimage,
							$origimage,
							0, 0, $x_coordinate, $y_coordinate,
							$new_w,
							$new_h,
							$crop_width,
							$crop_height
						);
					}else{
						if($this->_params['thumbdetail']==1){#TOP LEFT
							imagecopyresampled(
							 $thumbimage,
								$origimage,
								0, 0, 0, 0,
								$new_w,
								$new_h,
								$new_w,
								$new_h
							);
						}elseif($this->_params['thumbdetail']==2){#TOPRIGHT
							imagecopyresampled(
							 $thumbimage,
								$origimage,
								0, 0, $width_ori-$new_w, 0,
								$new_w,
								$new_h,
								$new_w,
								$new_h
							);
						}elseif($this->_params['thumbdetail']==3){#BOTTOMLEFT
							imagecopyresampled(
							 $thumbimage,
								$origimage,
								0, 0, 0, $height_ori-$new_h,
								$new_w,
								$new_h,
								$new_w,
								$new_h
							);
						}elseif($this->_params['thumbdetail']==4){#BOTTOMRIGHT
							imagecopyresampled(
							 $thumbimage,
								$origimage,
								0, 0, $width_ori-$new_w, $height_ori-$new_h,
								$new_w,
								$new_h,
								$new_w,
								$new_h
							);
						}else{#1:1
							imagecopyresampled(
							 $thumbimage,
								$origimage,
								0, 0, 0, 0,
								$new_w,
								$new_h,
								$width_ori,
								$height_ori
							);
						}
					}
					if($this->_params['watermark']){
						imagegif($thumbimage, $thumbDir.$image_hash);
					}else{
						imagegif($thumbimage, $thumbDir.$images[$a]['filename']);
					}
    }elseif(substr(strtolower($filenamethumb), -3)== 'jpg'){
					if($this->_params['watermark']){
					$origimage=imagecreatefromjpeg($wmDir.$image_hash);
					}else{
					$origimage=imagecreatefromjpeg($imagesDir.$images[$a]['filename']);
					}
					$width_ori=imagesx($origimage);
					$height_ori=imagesy($origimage);
					$thumbimage=imagecreatetruecolor($new_w, $new_h);
					if($this->_params['crop'] AND ($this->_params['crop_factor'] > 0 AND $this->_params['crop_factor'] < 100)){
					list($crop_width, $crop_height, $x_coordinate, $y_coordinate)=$this->crop($width_ori, $height_ori);
					imagecopyresampled($thumbimage, $origimage, 0, 0, $x_coordinate, $y_coordinate, $new_w, $new_h, $crop_width, $crop_height);
					}else{
					if($this->_params['thumbdetail']== 1){
					imagecopyresampled($thumbimage, $origimage, 0, 0, 0, 0, $new_w, $new_h, $new_w, $new_h);
					}elseif($this->_params['thumbdetail']== 2){
					imagecopyresampled($thumbimage, $origimage, 0, 0, $width_ori - $new_w, 0, $new_w, $new_h, $new_w, $new_h);
					}elseif($this->_params['thumbdetail']== 3){
					imagecopyresampled($thumbimage, $origimage, 0, 0, 0, $height_ori - $new_h, $new_w, $new_h, $new_w, $new_h);
					}elseif($this->_params['thumbdetail']== 4){
					imagecopyresampled($thumbimage, $origimage, 0, 0, $width_ori - $new_w, $height_ori - $new_h, $new_w, $new_h, $new_w, $new_h);
					}else{
					imagecopyresampled($thumbimage, $origimage, 0, 0, 0, 0, $new_w, $new_h, $width_ori, $height_ori);
					}
					}
					if($this->_params['watermark']){
					imagejpeg($thumbimage, $thumbDir.$image_hash, $this->_params['quality']);
					}else{
					imagejpeg($thumbimage, $thumbDir.$images[$a]['filename'], $this->_params['quality']);
					}
    }elseif(substr(strtolower($filenamethumb), -3)== 'png'){
					if($this->_params['watermark']){
					$origimage=imagecreatefrompng($wmDir.$image_hash);
					}else{
					$origimage=imagecreatefrompng($imagesDir.$images[$a]['filename']);
					}
					$width_ori=imagesx($origimage);
					$height_ori=imagesy($origimage);
					$thumbimage=imagecreatetruecolor($new_w, $new_h);
					if($this->_params['crop'] AND ($this->_params['crop_factor'] > 0 AND $this->_params['crop_factor'] < 100)){
					list($crop_width, $crop_height, $x_coordinate, $y_coordinate)=$this->crop($width_ori, $height_ori);
					imagecopyresampled($thumbimage, $origimage, 0, 0, $x_coordinate, $y_coordinate, $new_w, $new_h, $crop_width, $crop_height);
					}else{
					if($this->_params['thumbdetail']== 1){
					imagecopyresampled($thumbimage, $origimage, 0, 0, 0, 0, $new_w, $new_h, $new_w, $new_h);
					}elseif($this->_params['thumbdetail']== 2){
					imagecopyresampled($thumbimage, $origimage, 0, 0, $width_ori - $new_w, 0, $new_w, $new_h, $new_w, $new_h);
					}elseif($this->_params['thumbdetail']== 3){
					imagecopyresampled($thumbimage, $origimage, 0, 0, 0, $height_ori - $new_h, $new_w, $new_h, $new_w, $new_h);
					}elseif($this->_params['thumbdetail']== 4){
					imagecopyresampled($thumbimage, $origimage, 0, 0, $width_ori - $new_w, $height_ori - $new_h, $new_w, $new_h, $new_w, $new_h);
					}else{
					imagecopyresampled($thumbimage, $origimage, 0, 0, 0, 0, $new_w, $new_h, $width_ori, $height_ori);
					}
					}
					if($this->_params['watermark']){
					imagepng($thumbimage, $thumbDir.$image_hash, $this->_params['quality_png']);
					}else{
					imagepng($thumbimage, $thumbDir.$images[$a]['filename'], $this->_params['quality_png']);
					}
    }
    imagedestroy($origimage);
    imagedestroy($thumbimage);
   }
  }
 }
}
/**
* Erzeugt verkleinerte Bilder im Ordner resizedImages, das aber noch den Originalnamen hat.
* @param $images Array numerische Indices mit Array des Bildnamens $images[0]['filename'], $images[1]['filename'],
* @return void
*/
private function resizeImages($images){
 $imagesDir=$this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/';
	$resizedDir=$imagesDir.'resizedimages/';
	$wmDir=$imagesDir.'wm/';
 if(!is_dir($resizedDir)){
  mkdir($resizedDir, 0755);
  file_put_contents($resizedDir.'index.html', '');
  file_put_contents($resizedDir.'.htaccess', "#Auto generated by sigeghsvs\ndeny from all");
 }
 $num=count($images);
 for($a=0; $a < $num; $a++){
		#ggf Font verkleinern in schwarzem Balken:
		if(isset($images[$a]['wmfontsizeghsvs'])){
			$wmfontsizeghsvs=$images[$a]['wmfontsizeghsvs'];
			#oberer Abstand im schwarzen Balken
			$addVertGhsvs=10;
		}else{
			$wmfontsizeghsvs=3;
			$addVertGhsvs=5;
		}
		#höhe schwarzer Beschriftungsbalken:
		$heightDescription=20;

		if($images[$a]['filenameghsvsnormal']){
			$titleghsvs=$images[$a]['filenameghsvsnormal'];
		}elseif($images[$a]['filenameghsvs']){
			$titleghsvs=$images[$a]['filenameghsvs'];
		}else{
			$titleghsvs=JURI::base();
		}


  if(!empty($images[$a]['filename'])){
   $filenamethumb=$resizedDir.$images[$a]['filename'];
   if(!file_exists($filenamethumb) || $this->_params['images_new'] != 0){
    list($new_h, $new_w)=$this->calculateSize($images[$a]['filename'], 0);
				$type=substr(strtolower($filenamethumb), -3);
				if($type=='gif'){
					$origimage=imagecreatefromgif($imagesDir.$images[$a]['filename']);
    }elseif($type=='jpg'){
					$origimage=imagecreatefromjpeg($imagesDir.$images[$a]['filename']);
	   }elseif($type=='png'){
					$origimage=imagecreatefrompng($imagesDir.$images[$a]['filename']);
				}
				if($type=='gif' || $type=='jpg' || $type=='png'){
					$width_ori=imagesx($origimage);
					$height_ori=imagesy($origimage);
					#OLD: $thumbimage=imagecreatetruecolor($new_w, $new_h);
					$thumbimage=imagecreatetruecolor($new_w, $new_h + $heightDescription);
					####schwarzer Beschriftungsbalken:
					/*
					ImageColorAllocate() gibt eine Farb-ID, die durch die angegebenen RGB-Werte bestimmt wird, zurück.
					$weiss   = ImageColorAllocate ($im, 255, 255, 255);
     $schwarz = ImageColorAllocate ($im, 0, 0, 0)
					*/
					####weiße Schrift:
					$tc=imagecolorallocate($thumbimage, 255, 255, 255);
					#imagestring ( resource $im , int $font , int $x , int $y , string $s , int $col )
					####String unten:####
					#imagestring($thumbimage, $wmfontsizeghsvs, 2, $new_h + $addVertGhsvs, utf8_decode ("$titleghsvs"), $tc);
					#imagecopyresampled($thumbimage, $origimage, 0, 0, 0, 0, $new_w, $new_h, $width_ori, $height_ori);
					####String oben:####
					imagestring($thumbimage, $wmfontsizeghsvs, 2, 0, utf8_decode ("$titleghsvs"), $tc);
					imagecopyresampled($thumbimage, $origimage, 0, 20, 0, 0, $new_w, $new_h, $width_ori, $height_ori);
				}
				if($type=='gif'){
					/* erzeugt eine GIF-Datei aus dem übergebenen image */
					imagegif($thumbimage, $resizedDir.$images[$a]['filename']);
				}elseif($type=='jpg'){
					imagejpeg($thumbimage, $resizedDir.$images[$a]['filename'], $this->_params['quality']);
				}elseif($type=='png'){
				 imagepng($thumbimage, $resizedDir.$images[$a]['filename'], $this->_params['quality_png']);
				}
				/* gibt den durch das Bild belegten Speicher wieder frei. */
				imagedestroy($origimage);
				imagedestroy($thumbimage);
   }
  }
 }
}
private function loadHeadData($sige_css=0){
 if(!empty($sige_css)){
  if(!$this->_params['turbo'] OR ($this->_params['turbo'] AND $this->_turbo_css_read_in)){
   $head="<style type='text/css'>\n".$sige_css."</style>";
   if($this->_turbo_css_read_in){
    file_put_contents($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/sige_turbo_css-'.$this->_params['lang'].'.txt', $head);
   }
  }else{
   $head=file_get_contents($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/sige_turbo_css-'.$this->_params['lang'].'.txt');
  }
 }else{
  $head=array();
  if($_SESSION['sigcountarticles']==0){
   $head[]='<link rel="stylesheet" href="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/sigeghsvs.css" type="text/css" media="screen" />';

      if($this->_params['js']== 0){
          if($this->_params['lang']== "de-DE"){
              $head[]='<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/slimbox.js"></script>';
          }else{
              $head[]='<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/slimbox_en.js"></script>';
          }

          $head[]='<link rel="stylesheet" href="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/slimbox.css" type="text/css" media="screen" />';
      }elseif($this->_params['js']== 1){
          if($this->_params['lang']== "de-DE"){
              $head[]='<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/lytebox.js"></script>';
          }else{
              $head[]='<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/lytebox_en.js"></script>';
          }
          $head[]='<link rel="stylesheet" href="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/lytebox.css" type="text/css" media="screen" />';
      }elseif($this->_params['js']== 2){
          if($this->_params['lang']== "de-DE"){
              $head[]='<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/shadowbox.js"></script>';
          }else{
              $head[]='<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/shadowbox_en.js"></script>';
          }

          $head[]='<link rel="stylesheet" href="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/shadowbox.css" type="text/css" media="screen" />';
          $head[]='<script type="text/javascript">Shadowbox.init();</script>';
      }elseif($this->_params['js']== 3){
          $head[]='<script type="text/javascript" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/milkbox.js"></script>';
          $head[]='<link rel="stylesheet" href="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/milkbox.css" type="text/css" media="screen" />';
      }
  }

  $head="\n".implode("\n", $head)."\n";
}

$document=JFactory::getDocument();
	if($document instanceof JDocumentHTML){
		$document->addCustomTag($head);
	}
}
private function htmlImage($image, &$html, $noshow, $file_info, $a, $filenameghsvs=false){

 if(!empty($image)){
  $imagename=substr($image, 0, -4);
  $type=substr(strtolower($image), -3);
		if($filenameghsvs){
   $image_hash=$filenameghsvs.'.'.$type;
		}else{
			$image_hash=$this->encrypt($imagename).'.'.$type;
		}
  $file_info_set=false;
  if(!empty($file_info)){
			foreach($file_info as $value){
				if($value[0]== $image){
					$image_title=$value[1];
					#$title4ATag=htmlentities($image_title, ENT_QUOTES, 'utf-8');
					if(isset($value[2])){
						$image_description=$value[2];
						$descrGhsvs=htmlentities($value[2], ENT_QUOTES, 'utf-8');
					}else{
						$image_description=$descrGhsvs=false;
					}
					$file_info_set=true;
					break;
				}
			}
  }
  if(!$file_info_set){
   $image_title=$imagename;
   $image_description=false;
  }

if(empty($noshow)){
 if($this->_params['list']){
  $html .= '<li>';
 }else{
  $html .= '<li class="sige_cont_'.$_SESSION["sigcount"].'"><span class="sige_thumb">';
 }
}
if($this->_params['image_link'] && empty($noshow)){
 $html .= '<a href="http://'.$this->_params['image_link'].'" title="'.$this->_params['image_link'].'" ';
 if($this->_params['image_link_new']){
  $html .= 'target="_blank"';
 }
 $html .= '>';
}elseif($this->_params['noslim'] && $this->_params['css_image'] && empty($noshow)){
 $html .= '<a class="sige_css_image" href="#sige_thumbnail">';
}elseif(!$this->_params['noslim']){
 if($this->_params['watermark']){
  if(empty($noshow)){
   $html .= '<a href="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/wm/'.$image_hash.'"';
  }else{
   $html .= '<span style="display: none"><a href="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/wm/'.$image_hash.'"';
}
}else{
if($this->_params['resize_images']){
if(empty($noshow)){
$html .= '<a href="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/resizedimages/'.$image.'"';
}else{
$html .= '<span style="display: none"><a href="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/resizedimages/'.$image.'"';
}
}else{
if(empty($noshow)){
$html .= '<a href="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/'.$image.'"';
}else{
$html .= '<span style="display: none"><a href="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/'.$image.'"';
}
}
}
if(empty($noshow)){
if($this->_params['css_image']){
$html .= ' class="sige_css_image"';
}
}
if($this->_params['connect']){
if($this->_params['view']== 0){
$html .= ' rel="lightbox.sig'.$this->_params['connect'].'"';
}elseif($this->_params['view']== 1){
$html .= ' rel="lytebox.sig'.$this->_params['connect'].'"';
}elseif($this->_params['view']== 2){
$html .= ' rel="lyteshow.sig'.$this->_params['connect'].'"';
}elseif($this->_params['view']== 3){
$html .= ' rel="shadowbox[sig'.$this->_params['connect'].']"';
}elseif($this->_params['view']== 4){
$html .= ' data-milkbox="milkbox-'.$this->_params['connect'].'"';
}
}else{
if($this->_params['view']== 0){
$html .= ' rel="lightbox.sig'.$_SESSION["sigcount"].'"';
}elseif($this->_params['view']== 1){
$html .= ' rel="lytebox.sig'.$_SESSION["sigcount"].'"';
}elseif($this->_params['view']== 2){
$html .= ' rel="lyteshow.sig'.$_SESSION["sigcount"].'"';
}elseif($this->_params['view']== 3){
$html .= ' rel="shadowbox[sig'.$_SESSION["sigcount"].']"';
}elseif($this->_params['view']== 4){
$html .= ' data-milkbox="milkbox-'.$_SESSION["sigcount"].'"';
}
}
$html .= ' title="';


if($this->_params['image_info']){
 $html .= '&lt;strong&gt;&lt;em&gt;'.$image_title.'&lt;/em&gt;&lt;/strong&gt;';
 if($image_description && $this->_params['fileinfo_part2']){
  $html .= ' - '.$image_description;
 }
}



if($this->_params['download']==1){
 if($this->_params['watermark']){
  $html.=' &lt;a href=&quot;'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/download.php?img='.rawurlencode($this->_rootfolder.$this->_images_dir.'/wm/'.$image_hash).'&quot; title=&quot;Download&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/download.png&quot; /&gt;&lt;/a&gt; ';
	}else{
		if($this->_params['resize_images']){
			$html .= ' &lt;a href=&quot;'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/download.php?img='.rawurlencode($this->_rootfolder.$this->_images_dir.'/resizedimages/'.$image).'&quot; title=&quot;Download&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/download.png&quot; /&gt;&lt;/a&gt;';
		}else{
	$html .= ' &lt;a href=&quot;'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/download.php?img='.rawurlencode($this->_rootfolder.$this->_images_dir.'/'.$image).'&quot; title=&quot;Download&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/download.png&quot; /&gt;&lt;/a&gt;';
	}
	}
}
if(empty($noshow)){
$html .= '" >';
}else{
$html .= '"></a></span>';
}
}
if(empty($noshow)){
if(!$this->_params['list']){
if($this->_params['thumbs']){
$alt=$image_title;
$title=$image_title;
#$html .= '<img alt="'.$image_title.'" title="'.$image_title;
if($image_description){
$alt.=' - '.$image_description;
$title.=' - '.$image_description;
#$html .= ' - '.$image_description;
}
$html .= '<img alt="'.$alt.'" title="'.$title;
if($this->_params['watermark']){
$html .= '" src="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/thumbs/'.$image_hash.'" />';
}else{
$html .= '" src="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/thumbs/'.$image.'" />';
}
}else{
$html .= '<img alt="'.$image_title.'" title="'.$image_title;
if($image_description){
$html .= ' - '.$image_description;
}
if($this->_params['watermark']){
$html .= '" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/showthumb.php?img='.$this->_rootfolder.$this->_images_dir.'/wm/'.$image_hash.'&amp;width='.$this->_params['width'].'&amp;height='.$this->_params['height'].'&amp;quality='.$this->_params['quality'].'&amp;ratio='.$this->_params['ratio'].'&amp;crop='.$this->_params['crop'].'&amp;crop_factor='.$this->_params['crop_factor'].'&amp;thumbdetail='.$this->_params['thumbdetail'].'" />';
}else{
if($this->_params['resize_images']){
$html .= '" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/showthumb.php?img='.$this->_rootfolder.$this->_images_dir.'/resizedimages/'.$image.'&amp;width='.$this->_params['width'].'&amp;height='.$this->_params['height'].'&amp;quality='.$this->_params['quality'].'&amp;ratio='.$this->_params['ratio'].'&amp;crop='.$this->_params['crop'].'&amp;crop_factor='.$this->_params['crop_factor'].'&amp;thumbdetail='.$this->_params['thumbdetail'].'" />';
}else{
$html .= '" src="'.$this->_live_site.'/plugins/content/sigeghsvs/plugin_sigeghsvs/showthumb.php?img='.$this->_rootfolder.$this->_images_dir.'/'.$image.'&amp;width='.$this->_params['width'].'&amp;height='.$this->_params['height'].'&amp;quality='.$this->_params['quality'].'&amp;ratio='.$this->_params['ratio'].'&amp;crop='.$this->_params['crop'].'&amp;crop_factor='.$this->_params['crop_factor'].'&amp;thumbdetail='.$this->_params['thumbdetail'].'" />';
}
}
}
}elseif($this->_params['list']){
 $html .= $image_title;
 if($image_description){
  $html .= ' - '.$image_description;
 }
}
if($this->_params['css_image'] AND !$this->_params['image_link']){
$html .= '<span>';
if($this->_params['watermark']){
$html .= '<img src="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/wm/'.$image_hash.'"';
}else{
if($this->_params['resize_images']){
$html .= '<img src="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/resizedimages/'.$image.'"';
}else{
$html .= '<img src="'.$this->_live_site.$this->_rootfolder.$this->_images_dir.'/'.$image.'"';
}
}
if($this->_params['css_image_half'] AND !$this->_params['list']){
$imagedata=getimagesize($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/'.$image);
$html .= ' width="'.($imagedata[0] / 2).'" height="'.($imagedata[1] / 2).'"';
}
$html .= ' alt="'.$image_title.'" title="'.$image_title;
if($image_description){
$html .= ' - '.$image_description;
}
$html .= '" /></span>';
}
if(!$this->_params['noslim'] OR $this->_params['image_link'] OR $this->_params['css_image']){
$html .= '</a>';
}
if($this->_params['caption'] AND !$this->_params['list']){
if($this->_params['single'] AND !empty($this->_params['scaption'])){
$html .= '</span><span class="sige_caption">'.$this->_params['scaption'].'</span></li>';
}else{
$html .= '</span><span class="sige_caption">'.$image_title.'</span></li>';
}
}
if($this->_params['list']){
 $html.='</li>';
}elseif(!$this->_params['caption']){
 $html.='</span></li>';
}
}
}
 if($this->_params['column_quantity'] AND empty($noshow)){
  if(($a + 1) % $this->_params['column_quantity']== 0){
   $html.='<br class="sige_clr"/>';
  }
 }
}
private function calculateSize($image, $thumbnail){
 if($this->_params['resize_images'] AND !$thumbnail){
  $new_w=$this->_params['width_image'];
  if($this->_params['ratio_image']){
   $imagedata=getimagesize($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/'.$image);
   $new_h=(int) ($imagedata[1] * ($new_w / $imagedata[0]));
   if($this->_params['height_image'] AND ($new_h > $this->_params['height_image'])){
    $new_h=$this->_params['height_image'];
    $new_w=(int) ($imagedata[0] * ($new_h / $imagedata[1]));
   }
  }else{
   $new_h=$this->_params['height_image'];
  }


 }else{
		$new_w=$this->_params['width'];
		if($this->_params['ratio']){
			$imagedata=getimagesize($this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/'.$image);
			$new_h=(int) ($imagedata[1] * ($new_w / $imagedata[0]));
			if($this->_params['height'] AND ($new_h > $this->_params['height'])){
				$new_h=$this->_params['height'];
				$new_w=(int) ($imagedata[0] * ($new_h / $imagedata[1]));
			}
		}else{
		 $new_h=$this->_params['height'];
		}
}
$ret=array((int) $new_h, (int) $new_w);
return ($ret);
}
private function calculateMaxThumbnailSize($images){
$max_height=array();
$max_width=array();
foreach($images as $image){
  list($max_height[], $max_width[])=$this->calculateSize($image['filename'], 1);
}
rsort($max_height);
rsort($max_width);
$this->_thumbnail_max_height=$max_height[0];
$this->_thumbnail_max_width=$max_width[0];
}
/**
* captions.txt auslesen
* @return array $file_info
*/
private function getFileInfo(){
 $file_info=false;
 $captions_lang=$this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/captions-'.$this->_params['lang'].'.txt';
 $captions_txtfile=$this->_absolute_path.$this->_rootfolder.$this->_images_dir.'/captions.txt';
 if(file_exists($captions_lang)){
  $captions_file=file($captions_lang);
  $count=0;
  foreach($captions_file as $value){
   $captions_line=explode('|', $value);
   $file_info[$count]=$captions_line;
   $count++;
  }
 }elseif(file_exists($captions_txtfile) AND !file_exists($captions_lang)){
  $captions_file=file($captions_txtfile);
  $count=0;
  foreach($captions_file as $value){
   $captions_line=explode('|', $value);
   $file_info[$count]=$captions_line;
   $count++;
  }
 }
	#volli ghsvs
	foreach($file_info as $idx=>$arr){
		foreach($arr as $k=>$v){
			$file_info[$idx][$k]=trim($v);
		}
	}
 return $file_info;
}
private function crop($width_ori, $height_ori){
if($width_ori > $height_ori){
  $biggest_side=$width_ori;
}else{
  $biggest_side=$height_ori;
}
$crop_percent=(1 - ($this->_params['crop_factor'] / 100));
if(!$this->_params['ratio'] AND ($this->_params['width']== $this->_params['height'])){
  $crop_width=$biggest_side * $crop_percent;
  $crop_height=$biggest_side * $crop_percent;
}elseif(!$this->_params['ratio'] AND ($this->_params['width'] != $this->_params['height'])){
  if(($width_ori / $this->_params['width']) < ($height_ori / $this->_params['height'])){
      $crop_width=$width_ori * $crop_percent;
      $crop_height=($this->_params['height'] * ($width_ori / $this->_params['width'])) * $crop_percent;
  }else{
      $crop_width=($this->_params['width'] * ($height_ori / $this->_params['height'])) * $crop_percent;
      $crop_height=$height_ori * $crop_percent;
  }
}else{
  $crop_width=$width_ori * $crop_percent;
  $crop_height=$height_ori * $crop_percent;
}
$x_coordinate=($width_ori - $crop_width) / 2;
$y_coordinate=($height_ori - $crop_height) / 2;
$ret=array($crop_width, $crop_height, $x_coordinate, $y_coordinate);
return $ret;
}
}
