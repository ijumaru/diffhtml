<?php
class ClassParser {
	private $fileName;
	private $rms;
	public function __construct($fileName, $rms) {
		$this->fileName = $fileName;
		$this->rms = $rms;
	}
	public function exec() {
		$fo = new SplFileObject($this->fileName);
		$methods = array();
		$datas = array();
		while (!$fo->eof()) {
			$line = $fo->fgets();
			$patternClass = '/^[ -+]\t*( *[a-zA-Z0-9]*)*?class ([a-zA-Z0-9]+) .*?{/i';
			if (preg_match($patternClass, $line, $matches) === 1) {
				$i = count($datas);
				$datas[$i]["class"] = $matches[2];
				if ($i > 0) {
					$datas[$i - 1]["method"] = $methods;
					$methods = array();
				}
			}
			$patternMethod = '/^[ -+]\t*( *[a-zA-Z0-9]*)*? ([a-zA-Z0-9]*?) *?\(.*?\).*?{/i';
			if (preg_match($patternMethod, $line, $matches) === 1 && !empty($matches[2])) {
				$methods[] = $matches[2];
			}
		}
		if (count($methods) > 0) {
			$datas[count($datas) - 1]["method"] = $methods;
		}
		$contents = "";
		$url = "http://fwnizi.is.sei.co.jp/saaRmsRakWF21/D01_Designing/".$this->rms."/Rev.001/src/";
		foreach ($datas as $data) {
			$contents.= PHP_EOL."<tr>".PHP_EOL;
			$contents.= '<td><a href="'.$url.$data["class"].'.html">'.$data["class"].".</a></td>".PHP_EOL;
			$isFirst = true;
			foreach ($data["method"] as $method) {
				if ($isFirst) {
					$contents.= "<td>".$method."</td>".PHP_EOL;
					$contents.= "<td></td>".PHP_EOL;
					$contents.= "</tr>".PHP_EOL;
					$isFirst = false;
				} else {
					$contents.= PHP_EOL."<tr>".PHP_EOL;
					$contents.= "<td></td>".PHP_EOL;
					$contents.= "<td>".$method."</td>".PHP_EOL;
					$contents.= "<td></td>".PHP_EOL;
					$contents.= "</tr>".PHP_EOL;
				}
			}
		}
		file_put_contents("isdoc_body.txt", $contents);
	}
}