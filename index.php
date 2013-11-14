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
	$cp = new ClassParser($file);
	$files = $cp->getFiles();
	foreach ($files as $key => $file) {
		$contents = $style."<pre>".$file."</pre>";
		$fileNameArray = explode("/", $key);
		file_put_contents($fileNameArray[count($fileNameArray) - 1].".html", $contents);
		$contents = "";
	}
	$datas = $cp->getData();
	$contents = "";
	$url = "http://fwnizi.is.sei.co.jp/saaRmsRakWF21/D01_Designing/".$_POST["rms"]."/Rev.001/src/";
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
