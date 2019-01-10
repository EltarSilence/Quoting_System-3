<?php
	abstract class ZView{

		protected static $dir = 'view/';

		protected static $app = "app.html";

		public static function getView($content, $base = "", $data = []){
			$str = file_get_contents(ZView::$app);
			if(sizeof($data) > 0 ){
				foreach($data as $k => $v){
					$str = '<?php $'.$k.' = json_decode('."'".json_encode($v)."'".', true); ?>'.$str;
				}
			}
			$str = str_replace('@base', ($base == "" ? "" : '<base href="'.$base.'" />'), $str);
			preg_match_all('/@include\([a-zA-Z\'"]*\)/', $str, $match);
			for($i = 0; $i < sizeof($match[0]); $i++){
				$match[1][$i] = str_replace(["'", '"'], ["", ""], get_string_between($match[0][$i], "(", ")"));
			}
			$match = ZView::getInclude($content, $match);
			for($i = 0; $i < sizeof($match[0]); $i++){
				$str = str_replace($match[0][$i], $match[2][$i], $str);
			}
			$str = preg_replace('/@for(.*)/', '<?php for$1{ ?>', $str);
			$str = preg_replace('/@endfor/', '<?php } ?>', $str);
			$str = preg_replace('/@if(.*)/', '<?php if$1{ ?>', $str);
			$str = preg_replace('/@else/', '<?php }else{ ?>', $str);
			$str = preg_replace('/@elseif(.*)/', '<?php }else if$1{ ?>', $str);
			$str = preg_replace('/@endif/', '<?php } ?>', $str);
			$str = preg_replace('/{{{(.*)}}}/', '<?php $1; ?>', $str);
			$str = preg_replace('/{{ (\S*) }}/', '<?php echo $1; ?>', $str);

			eval("?>".$str."<?php");
		}
		private static function getInclude($content, $match){
			$str = file_get_contents(ZView::$dir.$content.".html");
			for($i = 0; $i < sizeof($match[1]); $i++){
				$match[2][$i] = get_string_between($str, "@".$match[1][$i], "@end".$match[1][$i]);
			}
			return $match;
		}
	}
?>
