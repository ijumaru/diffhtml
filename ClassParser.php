<?php
class ClassParser {
	private $fileName;
	public function __construct($fileName) {
		$this->fileName = $fileName;
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
		foreach ($datas as $data) {
			$contents.= "<tr><td>".$data["class"]."</td>";
			$isFirst = true;
			foreach ($data["method"] as $method) {
				if ($isFirst) {
					$contents.= "<td>".$method."</td><td></td></tr>".PHP_EOL;
					$isFirst = false;
				} else {
					$contents.= "<tr><td></td><td>".$method."</td><td></td></tr>".PHP_EOL;
				}
			}
		}
		file_put_contents("isdoc_body.txt", $contents);
	}
}