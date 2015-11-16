<?php
/*
pagesing(현재페이지,페이지 표시구간,총페이지,페이지번호를 제외한 링크,표시타입->배열)
표시타입
$this->attr['style_current'] = 페이지 타입
$this->attr['style_pages'] = 페이지 타입
$this->attr['style_first'] = 처음으로
$this->attr['style_end'] =마지막으로
$this->attr['style_prev'] = 앞블럭단뒤
$this->attr['style_next'] = 다음 블럭단위
$this->attr['style_end'] =마지막으로
*/

class pages{
	var $attr = array();
	var $solv = array();
	var $result = array();
	function pages($param=array()){
		$this->__construct($param);
	}
	
	function __construct($param=array()){
		$this->attr = array('page'=>1,'total_page'=>1,'links'=>'','pageblocks'=>10,
							'style_first'=>'<img src="/images/common/btn_page_start.gif" border="0" align="absmiddle" alt="" />',
							'style_prev'=>'<img src="/images/common/btn_page_prev.gif" border="0" align="absmiddle" alt="" />&nbsp;',
							'style_page'=>'<span style="font-weight:500; width:24px; height:23px; line-height:23px; border:1px solid #ec4024; margin:0px 1px 1px 1px; color:#ec4024; cursor:pointer;">%u</span>', // 현재 페이지
							'style_next'=>'&nbsp;<img src="/images/common/btn_page_next.gif" border="0" align="absmiddle" alt="" />',
							'style_end'=>'<img src="/images/common/btn_page_end.gif" border="0" align="absmiddle" alt="" />',
							'style_pages'=>'<span style="width:24px; height:23px; line-height:23px; border:1px solid #dddddd; margin:0px 1px 1px 1px; color:#999999; cursor:pointer;">%u</span>', // 일반 페이지
							'style_page_sep'=>'');
		$this->_init($param);
	/*
		$this->attr = array('page'=>1,'total_page'=>1,'links'=>'','pageblocks'=>10,
							'style_first'=>'<img src="/images/common/btn_page_start.gif" border="0" align="absmiddle">',
							'style_prev'=>'이전 attr{pageblocks} 페이지',
							'style_page'=>'<span style="font-weight:bold">[%u]</span>', // 현재 페이지
							'style_next'=>'다음 attr{pageblocks} 페이지',
							'style_end'=>'<img src="/images/common/btn_page_end.gif" border=0 align="absmiddle" alt="" />',
							'style_pages'=>'[%u]', // 일반 페이지 
							'style_page_sep'=>'&nbsp;');
		$this->_init($param);
	*/
	}
	
	function _init($param=array()){
		$this->_attr($param);
		$this->solv = array();
		return $this;
	}
	
	function _attr(){
		$params = func_get_args();
		if(count($params)==1){
			if(is_array($params[0]) && count($params[0]) > 0){
				foreach($params[0] as $key=>$val){
					if(isset($this->attr[$key])) $this->attr[$key] = $val;
				}
			}else if(is_string($params[0])){
				return $this->attr[$params[0]];
			}
		}else if(is_string($params[0]) && isset($this->attr[$key])){
			$this->attr[$key] = $params[1];
		}
		return $this;
	}
	
	function _solv(){
		if(empty($this->attr['total_page']) || !is_numeric($this->attr['total_page']) ||  $this->attr['total_page'] < 1) $this->attr['total_page'] = 1;
		if(empty($this->attr['page']) || !is_numeric($this->attr['page']) ||  $this->attr['page'] < 1) $this->attr['page'] = 1;
		if(empty($this->attr['pageblocks']) || !is_numeric($this->attr['pageblocks']) ||  $this->attr['pageblocks'] < 1) $this->attr['pageblocks'] = 10;
		$this->attr['page'] = min($this->attr['page'],$this->attr['total_page']);
	
		$this->solv = array();
		$this->solv['st'] = floor(@(($this->attr['page']-1)/$this->attr['pageblocks']))*$this->attr['pageblocks'] + 1;
		if($this->solv['st'] < 1 || !is_numeric($this->solv['st'])) $this->solv['st'] = 1;
		$this->solv['ed'] = min(($this->solv['st'] + $this->attr['pageblocks'] -1),$this->attr['total_page']);

		//$this->solv["first"] 	= ($this->solv['st'] > $this->attr['pageblocks']*2)?1:'';
		$this->solv["first"] 	= 1;
		//$this->solv["prev"] 	= ($this->solv['st'] > $this->attr['pageblocks'])?($this->solv['st']-$this->attr['pageblocks']):'';		
		$this->solv["prev"] 	= ($this->solv['st'] > $this->attr['pageblocks'])?($this->solv['st']-$this->attr['pageblocks']):1;		
		$this->solv['pages'] 	= '&nbsp;';
		//$this->solv["next"] 	= ($this->solv['ed'] < $this->attr['total_page'])?($this->solv['ed']+1):'';
		$this->solv["next"] 	= ($this->solv['ed'] < $this->attr['total_page'])?($this->solv['ed']+1):$this->solv['ed'];		
		//$this->solv["end"] 		= ($this->solv['ed'] < $this->attr['total_page']-$this->attr['pageblocks'])?$this->attr['total_page']:'';
		$this->solv["end"] 		= $this->solv['ed'];
		
		$this->result = array();
		/*
		$this->result["first"] = '<a href="'.sprintf($this->attr['links'], 1) . '">'.sprintf($this->attr['style_first'], 1).'</a>';
		$this->result["prev"] = '<a href="'.sprintf($this->attr['links'], $this->solv['prev']) . '">'.sprintf($this->attr['style_prev'], $this->solv['prev']).'</a>';
		$this->result['pages'] = array();
		$this->result["next"] = '<a href="'.sprintf($this->attr['links'], $this->solv['next']) . '">'.sprintf($this->attr['style_next'], $this->solv['next']).'</a>';
		$this->result["end"] = '<a href="'.sprintf($this->attr['links'], $this->solv['end']) . '">'.sprintf($this->attr['style_end'], $this->solv['end']).'</a>';
		*/
		foreach($this->solv as $key=>$val){
			$stylekey = 'style_'.$key;
			if(!empty($val) && isset($this->attr[$stylekey]) && !empty($this->attr[$stylekey])){
				$stylestr = $this->attr[$stylekey];
				if(preg_match('/attr\{([a-zA-Z0-9_]+)\}/',$stylestr,$tmp)){					
					if(isset($this->attr[$tmp[1]])) $stylestr = str_replace('attr{'.$tmp[1].'}',$this->attr[$tmp[1]],$stylestr);
				}				
				$this->result[$key] = '<a href="'.sprintf($this->attr['links'],$val).'" rel="external">'.$stylestr. '</a>';
			}			
		}
		
		$st = $this->solv['st'];				
		$this->result['pages'] = array();
		while($st <= $this->solv['ed']){
			if($st == $this->attr['page']){
				array_push($this->result['pages'],sprintf($this->attr['style_page'], $st));
			}else{
				array_push($this->result['pages'],'<a href="'.sprintf($this->attr['links'], $st) . '"  rel="external">'.sprintf($this->attr['style_pages'], $st).'</a>');
			}
			$st++;
		}		
		$this->result['pages'] = (is_array($this->result['pages'])&&count($this->result['pages']) > 0)?implode($this->attr['style_page_sep'],$this->result['pages']):'';
	//	$this->result['fulltext'] = implode($this->attr['style_page_sep'],$this->result);
		$this->result['fulltext'] = implode('',$this->result);
		return $this;
	}
	
	function _result($key=NULL){
		if(empty($key) || !isset($this->result[$key])) return $this->result;
		else return $this->result[$key];
	}
}

?>