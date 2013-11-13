<?php
class ClassParser {
	private static $patternClass = '/^[ -+]\t*( *[a-zA-Z0-9]*)*?class ([a-zA-Z0-9]+) .*?{/i';
	private static $patternMethod = '/^[ -+]\t*( *[a-zA-Z0-9]*)*? ([a-zA-Z0-9]*?) *?\(.*?\).*?{/i';
	private $datas = array();
	private $lines = array();
	private $convertedLines = array();
	private $files = array();
	private $fileName;
	private $fileContent = "";

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
			$this->lines[] = $line;
			$convertedLine =  $this->convert($line);
			$this->convertedLines[] = $convertedLine;
			$this->fileContent.= $convertedLine;
			if (strpos($line, "\ No newline at end of file") !== false) {
				$this->files[$this->fileName] = $this->fileContent;
				$this->fileContent = "";
			}
			if (preg_match(self::$patternClass, $line, $matches) === 1) {
				$i = count($this->datas);
				$this->datas[$i]["class"] = $matches[2];
				if ($i > 0) {
					$this->datas[$i - 1]["method"] = $methods;
					$methods = array();
				}
			}
			if (preg_match(self::$patternMethod, $line, $matches) === 1 && !empty($matches[2])) {
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

	private function convert($line) {
		$line = str_replace("<", "&lt;", $line);
		$line = str_replace(">", "&gt;", $line);
		while (preg_match('/^[ +-]\t(.*?)$/i', $line, $matches) === 1) {
			$line = preg_replace('/^([ +-])\t(.*?)$/i', '$1    $2', $line);
		}
		$line = preg_replace('/^ (.*?)$/i', '$1', $line);
		if (strpos($line, "diff") === 0) {
			$line = "";
		} else if (strpos($line, "index") === 0) {
			$line = "";
		} else if (strpos($line, "@@") === 0) {
			$line = "";
		} else if (strpos($line, "+++") === 0) {
			$this->fileName = trim(str_replace("+++ b/", "", $line));
			$line = "";
		} else if (strpos($line, "---") === 0) {
			$this->fileName = trim(str_replace("--- a/", "", $line));
			$line = "";
		} else if (strpos($line, "+") === 0) {
			$line = str_replace("+", "", $line);
			$line = "<ins>".$line."</ins>";
		} else if (strpos($line, "-") === 0) {
			$line = str_replace("-", "", $line);
			$line = "<del>".$line."</del>";
		}
		return $line;
	}

	public function getConvertedLines() {
		return $this->convertedLines;
	}

	public function getFiles() {
		return $this->files;
	}
}