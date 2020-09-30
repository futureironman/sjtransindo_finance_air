<?php

function UploadPegawai($fupload_name){
	$vdir_upload = "../../images/pegawai/";
	$vfile_upload = $vdir_upload . $fupload_name;

	//Simpan gambar dalam ukuran sebenarnya
	move_uploaded_file($_FILES["fupload"]["tmp_name"], $vfile_upload);

	$ext= end(explode('.', $_FILES['fupload']['name']));
	//identitas file asli
	//untuk membuat ukuran small
	if($ext=="jpg" || $ext=="jpeg" || $ext=="JPG" || $ext=="JPEG")
	{
		$im_src = imagecreatefromjpeg($vfile_upload);
	}
	else if($ext=="png" || $ext=="PNG")
	{
		$im_src = imagecreatefrompng($vfile_upload);
	}
	else if($ext=="gif" || $ext=="GIF")
	{
		$im_src = imagecreatefromgif($vfile_upload);
	}
  
	//identitas file asli
	$src_width = imageSX($im_src);
	$src_height = imageSY($im_src);

	//Simpan dalam versi small 350 pixel
	//Set ukuran gambar hasil perubahan
	$dst_height = 640;
	$dst_width = ($dst_height/$src_height)*$src_width;  
	
	//proses perubahan ukuran
	$im = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);

	if($ext=="jpg" || $ext=="jpeg" || $ext=="JPG" || $ext=="JPEG")
	{
		imagejpeg($im,$vdir_upload . "upload_" . $fupload_name);
	}
	else if($ext=="png" || $ext=="PNG")
	{
		imagepng($im,$vdir_upload . "upload_" . $fupload_name);
	}
	else if($ext=="gif" || $ext=="GIF")
	{
		imagegif($im,$vdir_upload . "upload_" . $fupload_name);
	}

	//Hapus gambar di memori komputer
	
	unlink("../../images/pegawai/$fupload_name");
	imagedestroy($im_src);
	imagedestroy($im);
}

function UploadProduk($fupload_name){
	$vdir_upload = "../../images/produk/";
	$vfile_upload = $vdir_upload . $fupload_name;

	//Simpan gambar dalam ukuran sebenarnya
	move_uploaded_file($_FILES["fupload"]["tmp_name"], $vfile_upload);

	$ext= end(explode('.', $_FILES['fupload']['name']));
	//identitas file asli
	//untuk membuat ukuran small
	if($ext=="jpg" || $ext=="jpeg" || $ext=="JPG" || $ext=="JPEG")
	{
		$im_src = imagecreatefromjpeg($vfile_upload);
	}
	else if($ext=="png" || $ext=="PNG")
	{
		$im_src = imagecreatefrompng($vfile_upload);
	}
	else if($ext=="gif" || $ext=="GIF")
	{
		$im_src = imagecreatefromgif($vfile_upload);
	}
  
	//identitas file asli
	$src_width = imageSX($im_src);
	$src_height = imageSY($im_src);

	//Simpan dalam versi small 350 pixel
	//Set ukuran gambar hasil perubahan
	$dst_height = 640;
	$dst_width = ($dst_height/$src_height)*$src_width;  
	
	//proses perubahan ukuran
	$im = imagecreatetruecolor($dst_width,$dst_height);
	imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);

	if($ext=="jpg" || $ext=="jpeg" || $ext=="JPG" || $ext=="JPEG")
	{
		imagejpeg($im,$vdir_upload . "upload_" . $fupload_name);
	}
	else if($ext=="png" || $ext=="PNG")
	{
		imagepng($im,$vdir_upload . "upload_" . $fupload_name);
	}
	else if($ext=="gif" || $ext=="GIF")
	{
		imagegif($im,$vdir_upload . "upload_" . $fupload_name);
	}

	//Hapus gambar di memori komputer
	
	unlink("../../images/produk/$fupload_name");
	imagedestroy($im_src);
	imagedestroy($im);
}
?>
