<?php
require_once("ClassParser.php");
if (isset($_FILES) && isset($_FILES["file"])) {
	$file = $_FILES['file']["tmp_name"];
}
if (!empty($file)) {
	$fo = new SplFileObject($file);
	$contents = "";
	$style = '<style type="text/css">'.PHP_EOL;
	$style.= 'ins { color:blue }'.PHP_EOL;
	$style.= 'del { color:red }'.PHP_EOL;
	$style.= '</style>'.PHP_EOL;
	while (!$fo->eof()) {
		$line = $fo->fgets();
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
			$fileName = trim(str_replace("+++ b/", "", $line));
			$line = "";
		} else if (strpos($line, "---") === 0) {
			$fileName = trim(str_replace("--- a/", "", $line));
			$line = "";
		} else if (strpos($line, "+") === 0) {
			$line = str_replace("+", "", $line);
			$line = "<ins>".$line."</ins>";
		} else if (strpos($line, "-") === 0) {
			$line = str_replace("-", "", $line);
			$line = "<del>".$line."</del>";
		}
		$contents.= $line;
		if (strpos($line, "\ No newline at end of file") !== false) {
			$contents = $style."<pre>".$contents."</pre>";
			$fileName = str_replace("/", "_", $fileName);
			file_put_contents($fileName.".html", $contents);
			$contents = "";
		}
	}
	$cp = new ClassParser($file, $_POST["rms"]);
	$cp->exec();
}
?>
<form enctype="multipart/form-data" action="index.php" method="post">
	<input type="submit" value="登録">
	<table>
		<tbody>
			<tr>
				<th>RMS</th>
				<td>
					<input type="number" name="rms">
				</td>
			</tr>
			<tr>
				<th>ファイル</th>
				<td>
					<input type="file" name="file">
				</td>
			</tr>
		</tbody>
	</table>
	<input type="submit" value="登録">
</form>
