<?php

class image {
	public static function querylinks($id) {
		$row = db()->query("SELECT * FROM images WHERE id='$id' LIMIT 1")->fetch_assoc();
		if(!$row) return;
		return self::getlinks($row);
	}
	public static function getlinks(&$row) {
		$image = 'http://'.SITE_DOMAIN.'/s/i/'.$row['id'].'.'.$row['ext'];
		$thumb = 'http://'.SITE_DOMAIN.'/s/t/'.$row['id'].'.'.$row['ext_thumb'];
		return array($image, $thumb, $row);
	}

	public static function download($url, $force_handling = true, &$error = NULL) {
		$data = my_file_get_contents($url);
		if(!$data) {
			$error = 'DOWNLOAD_ERROR';
			return;
		}

		if(preg_match('~([^\?&=/]+\.(jpe?g|gif|png))([\?#].*)?$~i', $url, $rv)) $name = $rv[1];
		else $name = 'noname.jpg';

		return self::insert($data, $name, $force_handling, $error);
	}
	public static function insert($data, $name, $force_handling, &$error = NULL) {
		$id = ilchk($data);
		$subid = substr($id, 0, 2);

		$name = es($name);

		if(db()->query("SELECT * FROM images WHERE id='$id' LIMIT 1")->num_rows == 1) {
			if($force_handling) $update = true;
			else return $id;
		} else $update = false;

		$ext = strtolower(substr(strtolower(strrchr($name, '.')), 1));
		if($ext != 'jpg' and $ext != 'gif' and $ext != 'png') $ext="jpg";

		$file = "$id.$ext";
		$local_image = IMAGE_DIRECTORY."/$subid/$file";
		$local_thumb = THUMB_DIRECTORY."/$subid/$id";
		$size = strlen($data);

		$fh = fopen($local_image, 'w');
		if(!$fh) {
			$error = "Fehler beim speichern des Covers ($local_image)";
			return;
		}
		fwrite($fh, $data);
		fclose($fh);
		@chmod($local_image, 0666);

		list($width, $height) = getimagesize($local_image);
		if(empty($width) or empty($height)) {
			if(file_exists($local_image)) unlink($local_image);
			$error = "Fehler beim &ouml;ffnen des runtergeladenen Bildes ($local_image [$width x $height])";
			return;
		}

		$ext_thumb = $ext;
		$wh = self::create_thumb($local_image, $local_thumb, 122, 173, 70, $local_image, $ext, $ext_thumb);
		if(empty($wh)) {
			if(file_exists($local_image)) unlink($local_image);
			if(file_exists($local_thumb)) unlink($local_thumb);
			$error = "Fehler beim erstellen des Thumbnails ($local_thumb)";
			return;
		}
		@chmod($local_thumb, 0644);

		self::spread(array(
			array($local_image, "images/$subid", $file),
			array($local_thumb, "thumbs/$subid", $id.".".$ext_thumb)
		));

		$name = preg_replace('~^(.+)\.[^\.]{3,4}$~', '\1', $name);
		$name = preg_replace('~[^a-z0-9\-_]~i', '_', $name);
		mt_srand(time());
		$rand = mt_rand() % 0xffffffff;
		$update = "bullshit=$rand, ext='".db()->escape_string($ext)."', ext_thumb='".db()->escape_string($ext_thumb)."', name='$name', size='$size', width=$width, height=$height, user_id='".(IS_LOGGED_IN ? USER_ID : 0)."'";
		$query = "
			INSERT INTO images
			SET id='$id', $update
			ON DUPLICATE KEY UPDATE $update";
		db()->query($query);

		return $id;
	}

	public static function create_thumb($in, &$out, $max_w, $max_h, $quality, $source = NULL, $ext = NULL, &$ext_thumb = NULL) {
		if(!$ext) $ext = strtolower(substr(strtolower(strrchr(isset($source)?$source:$in, '.') ), 1));
		switch($ext) {
		case "gif"; $src = imagecreatefromgif($in); $ext_thumb = 'gif'; break;
		case "png": $src = imagecreatefrompng($in); $ext_thumb = 'png'; break;
		case "bmp": $src = imagecreatefrombmp($in); $ext_thumb = 'jpg'; break;
		default: $src = imagecreatefromjpeg($in); $ext_thumb = 'jpg'; break;
		}
		if(!$src) {
			$fuck = '/tmp/img.err.'.time().'.'.$ext;
			trigger_error('IMAGE ERROR; REMOTE ADDR: '.$_SERVER['REMOTE_ADDR'].'; SAVED TO '.$fuck, E_USER_NOTICE);
			@file_put_contents($fuck, file_get_contents($in));
			return array();
		}

		$ext_thumb = 'jpg';
		$out .= ".".$ext_thumb;

		list($w, $h) = getimagesize($in);
		$nw = $w;
		$nh = $h;
		if($nh > $max_h) {
			$nw = ($w/$h)*$max_h;
			$nh = $max_h;
		}
		if($nw > $max_w) {//width is more important
			$nw = $max_w;
			$nh = ($h/$w)*$max_w;
		}
		$temp = imagecreatetruecolor($nw, $nh);
		imagecopyresampled($temp, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
		switch($ext_thumb) {
		case 'gif'; imagegif($temp, $out, $quality); break;
		case 'png': imagepng($temp, $out, $quality); break;
		default: imagejpeg($temp, $out, $quality); break;
		}
		imagedestroy($src);
		imagedestroy($temp);
		return array($w, $h, $nw, $nh, $ext_thumb);
	}

	public static function spread($files) {
		/*$errors = array();
		//self::spread_do('ssh', 'pichost', 22, 'webman', 'vzb54wihrefs', '/home/i/', $files, $errors);
		#self::spread_do('ssh', 'ro2', 22, 'webman', 'vzb54wihrefs', '/data/i/', $files, $errors);
		if($errors) print_r($errors);*/
	}
	private static function spread_do($type, $host, $port, $user, $pass, $basedir, &$files, &$errors) {
		switch($type) {
		default:
		case 'ftp':
			$s = ftp_ssl_connect($host, $port);
			if(!$s) {
				$error = "FTP Fehler (connect)";
				return;
			}
			if(!ftp_login($s, $user, $pass)) {
				$error = "FTP Fehler (login)";
				return;
			}
			foreach($files as $f) {
				if(!ftp_chdir($s, $basedir.$f[1])) {
					$error = "FTP Fehler (chdir(".$f[1]."))";
					return;
				}
				if(!ftp_put($s, $f[2], $f[0], FTP_BINARY)) {
					$error = "FTP Fehler (put(".$f[2]."))";
					return;
				}
			}
			ftp_close($s);
			break;

		case 'ssh':
			$s = ssh2_connect($host, $port);
			if(!$s) {
				$error = "SSH Fehler (connect)";
				return;
			}
			if(!ssh2_auth_password($s, $user, $pass)) {
				$error = "SSH Fehler (login)";
				return;
			}
			foreach($files as $f) {
				if(!@ssh2_scp_send($s, $f[0], $basedir.$f[1]."/".$f[2])) {
					$error = "SSH Fehler (scp_send(".$f[0]." -> ".$basedir.$f[1]."/".$f[2]."))";
					return;
				}
			}
			@fclose($s);
			break;
		}
	}
}

?>
