<?php
	abstract class ZModel{
		
		protected $dir = "model/";

		protected $name = "model";
		
		private $property = [];
		
		public function __construct($data = []){
			foreach($data as $k => $v){
				$this->property[$k] = $v;
			}
		}
		
		public function get(){
			$str = file_get_contents($this->dir.$this->name.".html");
			
			$str = preg_replace('/@for(.*)/', '<?php for$1{ ?>', $str);
			$str = preg_replace('/@endfor/', '<?php } ?>', $str);
			$str = preg_replace('/@if(.*)/', '<?php if$1{ ?>', $str);
			$str = preg_replace('/@else/', '<?php }else{ ?>', $str);
			$str = preg_replace('/@elseif(.*)/', '<?php }else if$1{ ?>', $str);
			$str = preg_replace('/@endif/', '<?php } ?>', $str);
			$str = preg_replace('/{{ \$(\S*) }}/', '<?php echo $this->property["$1"]; ?>', $str);
			
			ob_start();
			eval("?>".$str);
			$html = ob_get_contents();
			ob_end_clean();
			
			return $html;
		}
	}
?>