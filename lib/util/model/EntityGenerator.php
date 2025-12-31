<?php
/*
 * A database entity generation script which is to map the database
 * object to PHP object. This is a utility class which will create
 * the entity class by supplying the fields lst only
 */
class EntityGenerator {
	private $root;
	private $filename;
	private $fieldlist;
	private $path;
	private $str='';
	private $hasDependant=false;
	private $dependantStr="";
	private $hasConsts=false;
	private $consts="";
	private $hasExtend=false;
	private $extendStr="";

	function __construct($root,$filename,$fieldlist){
		$this->root=$root;
		$this->filename=$filename;
		$this->fieldlist=$fieldlist;

		$this->path=$root.'/'.$filename.'.class.php';
	}

	private function generateHeader(){
		$this->str.='<?php'."\n";
		if($this->hasDependant){
			$this->str.=$this->dependantStr;
		}
		$this->str.='class '.$this->filename;
		if($this->hasExtend){
			$this->str.=$this->extendStr;
		}
		$this->str.=' implements JsonSerializable {'."\n";
	}

	private function generateConsts(){
		if($this->hasConsts){
			$this->str.=$this->consts;
		}
	}

	private function generateProperties(){
		foreach($this->fieldlist as $field){
			$this->str.="\tprivate $".lcfirst($field).";\n";
		}
		$this->str.="\n";
	}

	private function generateConstructor(){
		$this->str.="\tfunction __construct(){\n";

		if($this->hasExtend){
			$this->str.="\t\tparent::__construct();\n";
		}
		$size=count($this->fieldlist);
		/*
		for($i=0;$i<$size;++$i){
			$this->str.='$'.lcfirst($this->fieldlist[$i]);
			if($i!=($size-1)){
				$this->str.=',';
			}
		}
		*/
		/*
		for($i=0;$i<$size;++$i){
			$this->str.="\t\t".'$this->'.lcfirst($this->fieldlist[$i]).' = $'.lcfirst($this->fieldlist[$i]).";\n";
		}
		*/
		$this->str.="\t}\n\n";

		return $this;
	}

	public function generateSetMethods(){
		foreach($this->fieldlist as $field){
			$this->str.="\t".'public function set'.$field.'($'.lcfirst($field).'){'."\n";
			$this->str.="\t\t".'$this->'.lcfirst($field).' = $'.lcfirst($field).";\n";
			$this->str.="\t}\n\n";
		}
		return $this;
	}

	public function generateGetMethods(){
		foreach($this->fieldlist as $field){
			$this->str.="\tpublic function get".$field."(){\n";
			$this->str.="\t\t".'return $this->'.lcfirst($field).";\n";
			$this->str.="\t}\n\n";
		}
		return $this;
	}

	public function generateUnimplementedMethod(){
		$this->str.="\n\t#[\ReturnTypeWillChange]\n\tpublic function jsonSerialize() {\n";

		if($this->hasExtend){
			$this->str.="\t\t".'$obj'."=parent::jsonSerialize();\n";

			foreach($this->fieldlist as $field){
				$this->str.="\t\t".'$obj["'.lcfirst($field).'"]'.' = $this->'.lcfirst($field).";\n";
			}

			$this->str.="\t\treturn ".'$obj'.";\n";
		}else{
			$this->str.="\t\treturn [\n";

			$size=count($this->fieldlist);
			$count=0;
			foreach($this->fieldlist as $field){
				if($size==$count+1){
					$this->str.="\t\t\t'".lcfirst($field)."'".'=>$this->'.lcfirst($field)."\n";
				}else{
					$this->str.="\t\t\t'".lcfirst($field)."'".'=>$this->'.lcfirst($field).",\n";
				}
				$count++;
			}
			$this->str.="\t\t];\n";
		}
		$this->str.="\t}\n\n";
		return $this;
	}

	private function generateFooter(){
		$this->str.='}';
	}

	public function generate(){
		$this->generateHeader();
		$this->generateConsts();
		$this->generateProperties();
		$this->generateConstructor();
		$this->generateSetMethods();
		$this->generateGetMethods();
		$this->generateUnimplementedMethod();
		$this->generateFooter();
		return file_put_contents($this->path,$this->str);
	}

	/*
	 *  Specify the dependant entities of the entity to be created
	 *  $dependList  : Dendant entity list
	 */
	public function depend($dependList){
		$this->dependantStr.='$DOCUMENT_ROOT=$_SERVER["DOCUMENT_ROOT"];'."\n";
		foreach($dependList as $dependantEntity=>$dependantEntityPath){
			$this->dependantStr.='include_once("$DOCUMENT_ROOT/'.$dependantEntityPath.'/'.$dependantEntity.'.class.php");'."\n";
		}
		$this->hasDependant=true;
		return $this;
	}

	/*
	 * Specify the const defined for the entity
	 * $const list
	 */
	public function appendConst($consts){
		foreach($consts as $field=>$value){
			$this->consts.="\tconst ".strtoupper($field)." = ".$value.";\n";
		}
		$this->consts.="\n";
		$this->hasConsts=true;
		return $this;
	}

	/*
	 * Specif the extended class
	 */
	public function extend($class){
		$this->hasExtend=true;
		$this->extendStr.=" extends ".$class;
		return $this;
	}
}