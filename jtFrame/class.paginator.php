<?php
/**
 * Based on pageNavigator.php used for Mambo 4.5.3
 * Still some fixing up that needs to be done - not entirely happy with 
 * the pagination algorithm, but it works when in a hurry
 */
define("_JT_PN_START", "Start");
define("_JT_PN_END", "End");
define("_JT_PN_PREVIOUS", "Previous");
define("_JT_PN_NEXT", "Next");
define("_JT_PN_RESULTS", "Currently viewing results");
define("_JT_PN_OF", "of");
define("_JT_PN_PAGE", "Currently viewing page"); 

class jtPaginator {
	/** @var int The record number to start dislpaying from */
	public $pageStart = null;
	/** @var int Number of rows to display per page */
	public $perPage = null;
	/** @var int Total number of rows */
	public $total = null;
	public $module = null;

	public function __construct( $total, $pageStart, $perPage, $module='' ) {
		$this->total = intval( $total );
		$this->pageStart = max( $pageStart, 0 );
		$this->perPage = max( $perPage, 0 );
		$this->module = $module;
	}
	
	/**
	* Writes the html for the pages counter, eg, Results 1-10 of x
	*/
	public function writePagesCounter() {
		$txt = '';
		$from_result = $this->pageStart+1;
		if ($this->pageStart + $this->perPage < $this->total) {
			$to_result = $this->pageStart + $this->perPage;
		} else {
			$to_result = $this->total;
		}
		if ($this->total > 0) {
			$txt .= _JT_PN_RESULTS." <strong>$from_result - $to_result</strong> "._JT_PN_OF." $this->total";
		}
		return $txt;
	}

	/**
	* Writes the html for the leafs counter, eg, Page 1 of x
	*/
	public function writeLeafsCounter() {
		$txt = '';
		$this_page = ceil( ($this->pageStart+1) / $this->perPage );
		$total_pages = ceil( $this->total / $this->perPage );
		if ($this->total > 0) {
			$txt .= _JT_PN_PAGE." <strong>$this_page</strong> "._JT_PN_OF." <strong>$total_pages</strong>";
		}
		return $txt;
	}

	/**
	* Writes the html links for pages, eg, previous, next, 1 2 3 ... x
	* @param string The basic link to include in the href
	*/
	public function writePagesLinks( $link ) {
		$txt = '';

		$displayed_pages = 10;
		$total_pages = ceil( $this->total / $this->perPage );
		$this_page = ceil( ($this->pageStart+1) / $this->perPage );
		$start_loop = (floor(($this_page-1)/$displayed_pages))*$displayed_pages+1;
		if ($start_loop + $displayed_pages - 1 < $total_pages) {
			$stop_loop = $start_loop + $displayed_pages - 1;
		} else {
			$stop_loop = $total_pages;
		}

		$link .= '&perPage='. $this->perPage;

		if ($this_page > 1) {
			$page = ($this_page - 2) * $this->perPage;
			$txt .= '<a href="' . $link . '&pageStart=0" class="pagenav" title="first page">&lt;&lt; '. _JT_PN_START .'</a> ';
			$txt .= '<a href="' . $link . '&pageStart=' . $page . '" class="pagenav" title="previous page">&lt; '. _JT_PN_PREVIOUS .'</a> ';
		} else {
//			$txt .= '<span class="pagenav">&lt;&lt; '. _JT_PN_START .'</span> ';
//			$txt .= '<span class="pagenav">&lt; '. _JT_PN_PREVIOUS .'</span> ';
		}

		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $this->perPage;
			if ($i == $this_page) {
				$txt .= '<span class="pagenav-current">'. $i .'</span> ';
			} else {
				$txt .= '<a href="' . $link .'&pageStart='. $page .'" class="pagenav">'. $i .'</a> ';
			}
		}

		if ($this_page < $total_pages) {
			$page = $this_page * $this->perPage;
			$end_page = ($total_pages-1) * $this->perPage;
			$txt .= '<a href="'.  $link .'&pageStart='. $page .'" class="pagenav" title="next page">'. _JT_PN_NEXT .' &gt;</a> ';
			$txt .= '<a href="'.  $link .'&pageStart='. $end_page .'" class="pagenav" title="end page">'. _JT_PN_END .' &gt;&gt;</a>';
		} else {
//			$txt .= '<span class="pagenav">'. _JT_PN_NEXT .' &gt;</span> ';
//			$txt .= '<span class="pagenav">'. _JT_PN_END .' &gt;&gt;</span>';
		}
		return $txt;
	}
}
?>