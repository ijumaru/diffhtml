<?php
class ClassParser {
	private $datas = array();

	public function __construct($fileName) {
		$this->parse($fileName);
	}

	private function parse($fileName) {
		$fo = new SplFileObject($fileName);
		$methods = array();
		$indent = 0;
		$isChanged = false;
		while (!$fo->eof()) {
			$line = $fo->fgets();
			$patternClass = '/^[ -+]\t*( *[a-zA-Z0-9]*)*?class ([a-zA-Z0-9]+) .*?{/i';
			if (preg_match($patternClass, $line, $matches) === 1) {
				$i = count($this->datas);
				$this->datas[$i]["class"] = $matches[2];
				if ($i > 0) {
					$this->datas[$i - 1]["method"] = $methods;
					$methods = array();
				}
			}
			$patternMethod = '/^[ -+]\t*( *[a-zA-Z0-9]*)*? ([a-zA-Z0-9]*?) *?\(.*?\).*?{/i';
			if (preg_match($patternMethod, $line, $matches) === 1 && !empty($matches[2])) {
				$method = $matches[2];
				$indent = 1;
			}
			if (strpos($line, "{") !== false && $indent > 0) { // strpos() >= 0は正しく動作しない
				$indent++;
			}
			if (strpos($line, "}") !== false && $indent > 0) {
				$indent--;
				if ($indent == 1 && $isChanged === true) {
					$methods[] = $method;
					$isChanged = false;
					$indent = 0;
					$method = "";
				}
			}
			if ($indent > 0 && (strpos($line, "+") === 0 || strpos($line, "-") === 0)) {
				$isChanged = true;
			}
		}
		if (count($methods) > 0) {
			$this->datas[count($this->datas) - 1]["method"] = $methods;
		}
	}

	public function getData() {
		return $this->datas;
	}
}