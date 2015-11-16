<?
function json_encode2($data) {
	switch (gettype($data)) {
		case 'boolean':
			return $data?'true':'false';
		case 'integer':
		case 'double':
			return $data;
		case 'string':
			return '"'.strtr($data, array('\\'=>'\\\\','"'=>'\\"')).'"';
		case 'array':
			$rel = false; // relative array?
			$key = array_keys($data);
			foreach ($key as $v) {
				if (!is_int($v)) {
					$rel = true;
					break;
				}
			}

			$arr = array();
			foreach ($data as $k=>$v) {
				$arr[] = ($rel?'"'.strtr($k, array('\\'=>'\\\\','"'=>'\\"')).'":':'').json_encode2($v);
			}

			return $rel?'{'.join(',', $arr).'}':'['.join(',', $arr).']';
		default:
			return '""';
	}
}
?>