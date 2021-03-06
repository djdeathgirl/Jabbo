<?php
error_reporting(0);
set_time_limit(50);
require_once("avatar.class.php");
$fout = false;
function add_img_url($x, $y, $src, $image1) {
	global $fout;
	$image2 = @imagecreatefromgif($src);
	if (!$image2) {
		$fout = true;
	}
	else {
		imagecopy($image1, $image2, $x, $y, 0, 0, 64, 110);
		imagedestroy($image2);
	}
	return $image1;
}
function add_img($x, $y, $image, $image1) {
	imagecopy($image1, $image, $x, $y, 0, 0, 64, 110);
	imagedestroy($image);
	return $image1;
}
if (ISSET($_GET['figure'])) {
	$figure = str_replace('/', '', $_GET['figure']);
	$cached = "cache/avatars/walk/".$figure.".gif";
	if(!file_exists($cached)){
		$direction = Array(1,2,3,4,5,6,7,0);
		$action = Array("std", "wlk");
		$walks = Array(0, 3, 2, 1);
		$images = Array();
		foreach ($direction as $key => $_direction) {
			$direction_ = $_direction;
			foreach ($action as $key2 => $_action) {
				$action_ = $_action;
				if ($action_ == "wlk") {
					foreach ($walks as $key3 => $_walk) {
						$walk_ = $_walk;
						$this_add = new Avatar($figure, $direction_, $direction_, $action_, 0, 0, $walk_);
						array_push($images, $this_add->Draw());
						unset($this_add);
					}
				}
				else {
					$this_add = new Avatar($figure, $direction_, $direction_, $action_, 0, 0, 0);
					array_push($images, $this_add->Draw());
					unset($this_add);
				}
			}
		}
		for ($i = 1; $i <= 4; $i++) { // zitten
			$direction = $i*2;
			if ($direction == 8) {
				$direction = 0;
			}
			$this_add = new Avatar($figure, $direction, $direction, "sit", 0, 0, 0);
			array_push($images, $this_add->Draw());
			unset($this_add);
		}
		header("Content-type: image/gif");
	    $image = @imagecreatetruecolor((sizeof($images)*64), 110);
		$transparent = imagecolorallocate($image, 1, 2, 3);
		imagecolortransparent($image, $transparent);
		imagefilledrectangle($image, 0, 0, (sizeof($images)*64), 110, $transparent);
		$x = 0;
		foreach ($images as $key => $imagesrc) {
			$image = add_img($x, 0, $imagesrc, $image);
	    	$x = $x+64;
		}
		if (!$fout) {
			imagegif($image, $cached);
		}
		imagedestroy($image);
	}
	if (!$fout) {
		header("Content-type: image/gif");
		echo file_get_contents($cached);
	}
	else {
		header("Content-type: image/gif");
		echo file_get_contents("images/ghost.gif");
	}
}
?>