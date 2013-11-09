<?php
if (isset($_FILES)) {
	$file = $_FILES['file']["tmp_name"];
}
if (!empty($file)) {
	$fo = new SplFileObject($file);
	$contents = "";
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
			$style = '<style type="text/css">';
			$style.= 'ins { color:blue }';
			$style.= 'del { color:red }';
			$style.= '</style>';
			$contents = $style."<pre>".$contents."</pre>";
			$fileName = str_replace("/", "_", $fileName);
			file_put_contents($fileName.".html", $contents);
			$contents = "";
		}
	}
}
?>
<form enctype="multipart/form-data" action="index.php" method="post">
	<input type="submit" value="登録">
	<table>
		<tbody>
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
