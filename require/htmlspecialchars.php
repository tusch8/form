<?php
if (!function_exists('h')) {
	function h($s) {
		return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
	}
}
