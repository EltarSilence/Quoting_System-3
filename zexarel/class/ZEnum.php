<?php
	class ZEnum{
		
		public static function __callStatic($name, $arguments){			
			if(property_exists(get_called_class(), $name)){
				if(array_key_exists($arguments[0], get_class_vars(get_called_class())[$name])){
					return get_class_vars(get_called_class())[$name][$arguments[0]];
				}else{
					if(array_key_exists("DEFAULT", get_class_vars(get_called_class())[$name])){
						return get_class_vars(get_called_class())[$name]["DEFAULT"];
					}else{
						throw new Exception("Indice dell enumeratore inesistente, DEFAULT inesistente");	
					}
				}
			}else{
				throw new Exception("Enumeratore non esiste");
			}
		}
	}
?>