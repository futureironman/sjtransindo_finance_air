<?php
	function DateToIndo($date) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
		$BulanIndo = array("Januari", "Februari", "Maret",
						   "April", "Mei", "Juni",
						   "Juli", "Agustus", "September",
						   "Oktober", "November", "Desember");
	
		$tahun = substr($date, 0, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 5, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 8, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;
		return($result);
	}
	
	function GetBulan($date) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
		$BulanIndo = array("Januari", "Februari", "Maret",
						   "April", "Mei", "Juni",
						   "Juli", "Agustus", "September",
						   "Oktober", "November", "Desember");
	
		$bulan = substr($date, 5, 2); // memisahkan format bulan menggunakan substring
		
		$result = $BulanIndo[(int)$bulan-1];
		return($result);
	}
	
	function GetBulan2($bulan) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
		$BulanIndo = array("Januari", "Februari", "Maret",
						   "April", "Mei", "Juni",
						   "Juli", "Agustus", "September",
						   "Oktober", "November", "Desember");
	
		$result = $BulanIndo[(int)$bulan-1];
		return($result);
	}
	
	function DateToIndo2($date) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
	
		$tahun = substr($date, 0, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 5, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 8, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $tgl . "/" .$bulan. "/". $tahun;
		return($result);
	}
	
	function DateToIndo3($date) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
		$BulanIndo = array("Jan", "Feb", "Mar",
						   "Apr", "Mei", "Jun",
						   "Jul", "Aug", "Sept",
						   "Oct", "Nov", "Des");
	
		$tahun = substr($date, 0, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 5, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 8, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;
		return($result);
	}
	
	
	function DateToIndo4($date) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
		$BulanIndo = array("Jan", "Feb", "Mar",
						   "Apr", "Mei", "Jun",
						   "Jul", "Aug", "Sept",
						   "Oct", "Nov", "Des");
	
		$tahun = substr($date, 0, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 5, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 8, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $tgl . " " . $BulanIndo[(int)$bulan-1];
		return($result);
	}
	
	function DateToIndo5($date) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
		$tahun = substr($date, 0, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 5, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 8, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $tgl . "-" .$bulan. "-". $tahun;
		return($result);
	}
	
	function DateToEng($date) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
	
		$tahun = substr($date, 6, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 3, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 0, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $tahun . "-" .$bulan. "-". $tgl;
		return($result);
	}
	
	/*function DateToEng($date) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
	
		$tahun = substr($date, 6, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 0, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 3, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $tahun . "-" .$bulan. "-". $tgl;
		return($result);
	}*/
	
	function DateToEng2($date) { // fungsi atau method untuk mengubah tanggal ke format indonesia
   // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
	
		$tahun = substr($date, 0, 4); // memisahkan format tahun menggunakan substring
		$bulan = substr($date, 5, 2); // memisahkan format bulan menggunakan substring
		$tgl   = substr($date, 8, 2); // memisahkan format tanggal menggunakan substring
		
		$result = $bulan . "/" .$tgl. "/". $tahun;
		return($result);
	}

	function hitung_umur($tanggal_lahir) {
		list($year,$month,$day) = explode("-",$tanggal_lahir);
		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;
		if ($month_diff < 0) $year_diff--;
			elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
		return $year_diff;
	}
	
	function beda_waktu($date1, $date2, $format = false) 
	{
		$diff = date_diff( date_create($date1), date_create($date2) );
		if ($format)
			return $diff->format($format);
		
		return array('y' => $diff->y,
					'm' => $diff->m,
					'd' => $diff->d,
					'h' => $diff->h,
					'i' => $diff->i,
					's' => $diff->s
				);
	}
?>
