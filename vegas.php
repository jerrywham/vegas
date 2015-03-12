<?php
/**
 * Classe vegas
 *
 * @version 1.0
 * @date	20/07/2013
 * @author	Jay Salvat, Cyril MAGUIRE
 **/
class vegas extends plxPlugin {
	
	private $file = '';
	private $cat = '';
	private $index = '';
	/**
	 * Constructeur de la classe
	 *
	 * @return	null
	 * @author	Cyril MAGUIRE 
	 **/	
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		# Ajouts des hooks
		$this->addHook('ThemeEndHead', 'ThemeEndHead');
		$this->addHook('ThemeEndBody', 'ThemeEndBody');
		$this->addHook('IndexEnd', 'IndexEnd');
	}

	public function onActivate() {
		$plxMotor = plxMotor::getInstance();
		$css = file_get_contents(PLX_PLUGINS.'vegas/vegas.css');
		$this->file = PLX_ROOT.$plxMotor->aConf['racine_themes'].$plxMotor->style.'/style.css';
		if (is_file($this->file))
			file_put_contents($this->file, $css, FILE_APPEND);

	}

	public function onDeActivate() {
		$plxMotor = plxMotor::getInstance();
		$cssToDel = file_get_contents(PLX_PLUGINS.'vegas/vegas.css');
		$this->file = PLX_ROOT.$plxMotor->aConf['racine_themes'].$plxMotor->style.'/style.css';
		$css = file_get_contents($this->file);
		$css = str_replace($cssToDel, '', $css);
		if (is_file($this->file))
			file_put_contents($this->file, $css, FILE_APPEND);

	}
	
	/**
	 * Méthode pour afficher la mise en page 
	 *
	 * @author Cyril MAGUIRE
	 */
	public function ThemeEndHead()
	{
		if (!is_file($this->file))
			echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'vegas/vegas.css" media="screen" />'."\n";
	}
	
	/**
	 * Méthode pour afficher le javascript
	 *
	 * @author Cyril MAGUIRE
	 */
	public function ThemeEndBody()
	{
		$plxMotor = plxMotor::getInstance();
		$plxShow = plxShow::getInstance();
		if ($plxMotor->mode == 'article') {
			$c = $plxMotor->plxRecord_arts->f('categorie');
			$c = explode(',', $c);
			foreach ($c as $idx => $catId) {
				# On va vérifier que la categorie existe
				if(isset($plxMotor->aCats[ $catId ])) {
					$id = intval($catId);
					if ($id > 4 && $id < 11) {
						# On recupere les infos de la categorie
						$this->cat = plxUtils::strCheck($plxMotor->aCats[ $catId ]['name']);
					}
				}
			}
		} elseif($plxMotor->mode == 'categorie') {
			ob_start();
			$plxShow->catName();
			$this->cat = ob_get_clean();
		}
		$this->cat = plxUtils::title2url(strtolower($this->cat));
		# Correspondance avec les images (clé = catégorie, valeur = nom image)
		$aImg = array(
			'home'=>'accueil',
			'danses'=>'latino',
			'aerolatino'=>'dance',
			'step'=>'step',
			'pilates'=>'pilates',
			'zumba'=>'zumba',
			'fitness'=>'fitness',
			'randonnees-pedestres'=>'rando'
		);
		$this->index = isset($aImg[$this->cat]) ? $aImg[$this->cat] : $aImg[ array_rand($aImg,1) ] ;

		echo "\t".'<script type="text/javascript">
				/* <![CDATA[ */
				if(typeof(jQuery) === "undefined") document.write(\'<script  type="text/javascript" src="'.PLX_PLUGINS.'vegas/jquery-1.9.1.min.js"><\/script>\');
				/* !]]> */
			</script>'."\n";
		echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'vegas/jquery.vegas.js"></script>'."\n";
		echo "
			<script type=\"text/javascript\">
				/* <![CDATA[ */
			    $(function() {
				    $.vegas({
				    src:'<?php \$plxShow->template();?>/img/".$this->index.".jpg',
					fade:5000, // milliseconds
				    loading:false
				    });
			    });
				/* !]]> */
			</script>
			<script type=\"text/javascript\">  
			/* <![CDATA[ */
			//Slider de l'entête
			   $(function(){  
			      setInterval(function(){  
			         $(\".sp-content ul\").animate({marginLeft:-350},800,function(){  
			            $(this).css({marginLeft:0}).find(\"li:last\").after($(this).find(\"li:first\"));  
			         })  
			      }, 10500);  
			   });  
			/* !]]> */
			</script>  
";
	}
}	