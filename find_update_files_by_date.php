<?php
//LICENSE :　https://opensource.org/licenses/mit-license.php
//(c) 2022 https://github.com/sada-m/tools/
header("Content-type: text/html; charset=utf-8\n");
header("X-Content-Type-Options: nosniff");
header('X-Frame-Options: DENY');
date_default_timezone_set('Asia/Tokyo');//Plese set your timezon
define('KWD_DIR',getcwd());//Must be an absolute path and end with "/"
$aryNG=array('upload/','xxxx/log/'); //not read file names
echo getcwd().'<br>';
$msg='';
if (!isset($_GET['y']) || !isset($_GET['m']) || !isset($_GET['d']) || !isset($_GET['h'])){
	$msg='push go button';
}
if ($msg==''){
	if (!is_numeric($_GET['y']) || !is_numeric($_GET['m']) || !is_numeric($_GET['d']) || !is_numeric($_GET['h'])){
		$msg='please number input!';
	}
}
if ($msg==''){
	$y=$_GET['y'];
	$m=$_GET['m'];
	$d=$_GET['d'];
	$h=$_GET['h'];
	if ($y<2000 || $y>3000)$msg='please check year!';
	if ($m<1 || $m>12)$msg='please check month!';
	if ($d<1 || $d>31)$msg='please check day!';
	if ($h<0 || $h>23)$msg='please check hour 0-23!';
}
if ($msg==''){
	define('KIJUN_TIME',date("U",mktime($h,0,0,$m,$d,$y)));//これ以降のファイルを抽出 mktime(8,30,0,7,28,2019)の場合、2019/07/28 8:30以降ということ
	echo 'KIJUN_TIME='.KIJUN_TIME.'<br>'."\n";
	$last='';
	$last_file='';
	function fnc_ck($dir){
		global $aryNG,$last,$last_file;
		$d=dir($dir);
		while (false !== ($entry = $d->read())) {
			if ($entry!='.' && $entry!='..' ){
				$ckpath=str_replace('//','/',$dir.'/'.$entry);
				$flg_read=true;
				foreach($aryNG as $vl){
					if (strpos('_'.$ckpath,$vl)>0 && $vl!='')$flg_read=false;
				}
				if ($flg_read){
					if (is_dir($ckpath)){
						 fnc_ck($ckpath);
					}else{
						$ft=filemtime($ckpath);
						if ($ft>KIJUN_TIME){
							$fdt=date("Y/m/d H:i:s",$ft);
							echo $ckpath.' ('.$fdt.$ft.')<br>'."\n";
							if ($last=='' || $fdt>$last){
								if (strpos($ckpath,'find_update_files_by_date.php')>0){
									//nothing
								}else{
									$last=$fdt;
									$last_file=$ckpath;
								}
							}
						}
					}
				}
			}
		}
		$d->close();
	}

	fnc_ck(KWD_DIR);
	echo '<br>last='.$last.$last_file.'<br>';
}else{
	$y=date("Y");
	$m=date("m");
	$d=1;
	echo $msg.'<br>';
	$jogai='';
	foreach($aryNG as $vl){
		if (trim($vl)!=''){
			if ($jogai!='')$jogai.=',';
			$jogai.=$vl;
		}
	}
?>
<form action="find_update_files_by_date.php" method="get">
year:<input type="text" style="50px;ime-mode:disabled;" name="y" maxlength="4" value="<?php echo $y;?>">
month:<input type="text" style="50px;ime-mode:disabled;" name="m" maxlength="2" value="<?php echo $m;?>">
day:<input type="text" style="50px;ime-mode:disabled;" name="d" maxlength="2" value="<?php echo $d;?>">
hour:<input type="text" style="50px;ime-mode:disabled;" name="h" maxlength="2" value="0">
<input type="submit" value="go">
（Exclusion:<?php echo $jogai;?>）
</form>
<p>
<?php
echo '<a href="find_update_files_by_date.php?y='.date("Y").'&m='.date("m").'&d='.date("d").'&h=0">today</a> | ';
$mktime=mktime(0,0,0,date("m"),(date("d")-1),date("Y"));
echo '<a href="find_update_files_by_date.php?y='.date("Y",$mktime).'&m='.date("m",$mktime).'&d='.date("d",$mktime).'&h=0">yestarday</a> | ';
$mktime=mktime(0,0,0,date("m"),(date("d")-7),date("Y"));
echo '<a href="find_update_files_by_date.php?y='.date("Y",$mktime).'&m='.date("m",$mktime).'&d='.date("d",$mktime).'&h=0">a week ago</a> | ';
$mktime=mktime(0,0,0,(date("m")-1),date("d"),date("Y"));
echo '<a href="find_update_files_by_date.php?y='.date("Y",$mktime).'&m='.date("m",$mktime).'&d='.date("d",$mktime).'&h=0">a month ago</a> | ';
?>
</p>
<br>※Coordinated Universal Time may be used depending on the environment, so please check that area as well.
<?php
}
?>
<br><a href="find_update_files_by_date.php">BACK</a><br>
end
