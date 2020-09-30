<?php
	function HitungUsia($date) {
		$biday = new DateTime($date);		
		$today = new DateTime();
	
		$diff = $today->diff($biday);
		
		$result=$diff->y." tahun ".$diff->m." bulan ".$diff->d." hari";
		return($result);
	}
?>
