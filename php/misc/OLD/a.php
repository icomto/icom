<?php

function barometer($max = 400, $sum = 350, $width = 500, $h1 = 20, $h2 = 10, $h3 = 20) {
	$interval = 4;
	$step = $max/$interval;
	$slice = $width/($interval * 4);
	$slice_error = $slice - floor($slice);
	$slice_error_sum = 0;
	$slice = floor($slice);
	
	$site = '<div class="barometer" style="width:'.$width.'px;height:'.($h1 + $h2 + $h3).'px;">';
	$site .= '<div class="barometer" style="width:100%;height:'.$h1.'px;border-bottom:1px #666 solid;">';
	
	for($i = 0; $i < $interval; $i++) {
		$slice_error_sum += $slice_error;
		if($slice_error_sum >= 1) {
			$s = ceil($slice_error_sum);
			$slice_error_sum -= $s;
			$s += $slice;
		}
		else $s = $slice;
		$site .= '<div style="border-right:1px #888 solid;height:'.floor($h1/4 + $h2).'px;'.($i ? 'margin-left:-1px;width:'.$s.'px;' : 'width:'.($s - 1).'px;').'margin-top:'.floor($h1 - $h1/4).'px;float:left;"></div>';
		$slice_error_sum += $slice_error;
		if($slice_error_sum >= 1) {
			$s = ceil($slice_error_sum);
			$slice_error_sum -= $s;
			$s += $slice;
		}
		else $s = $slice;
		$site .= '<div style="border-right:1px #888 solid;width:'.$s.'px;height:'.floor($h1/2 + $h2).'px;margin-left:-1px;margin-top:'.floor($h1/2).'px;float:left;"></div>';
		$slice_error_sum += $slice_error;
		if($slice_error_sum >= 1) {
			$s = ceil($slice_error_sum);
			$slice_error_sum -= $s;
			$s += $slice;
		}
		else $s = $slice;
		$site .= '<div style="border-right:1px #888 solid;width:'.$s.'px;height:'.floor($h1/4 + $h2).'px;margin-left:-1px;margin-top:'.floor($h1 - $h1/4).'px;float:left;"></div>';
		$slice_error_sum += $slice_error;
		if($slice_error_sum >= 1) {
			$s = ceil($slice_error_sum);
			$slice_error_sum -= $s;
			$s += $slice;
		}
		else $s = $slice;
		$site .= '<div style="border-right:1px #666 solid;width:'.$s.'px;height:'.($h1 + $h2).'px;margin-left:-1px;text-align:right;float:left;">'.(($i + 1)*$step).'</div>';
	}
	$site .= '</div>';
	$per = floor(($sum/$max)*100);
	$site .= '
		<div style="border-bottom:1px #666 solid;height:'.($h2 - 1).'px;">
			<div style="border-right:1px #666 solid;width:'.$per.'%;height:'.($h2 + $h3).'px;">
				<div style="background-color:green;width:100%;height:'.$h2.'px;"></div>
			</div>
		</div>
		<div style="width:'.$per.'%;height:'.$h3.'px;text-align:right;">'.$sum.'</div>
	</div>';
	return $site;
}

echo barometer();

?>
