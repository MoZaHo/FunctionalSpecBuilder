<?php

include("dompdf/autoload.inc.php");

use Dompdf\Dompdf;

if (!isset($_REQUEST['docid'])) {
	$_REQUEST['docid'] = 1;
}
$document_id = $_REQUEST['docid'];

$db = new PDO("mysql:host=localhost;dbname=functional_spec_builder","root","M0rn300S");
$dbs = $db->query("SELECT id, parent_id, title from chapter where document_id = " . $document_id . " order by parent_id, `order`, id");



$arrayCategories = array();

while(($row = $dbs->fetch(PDO::FETCH_ASSOC)) !== FALSE) {
	$arrayCategories[$row['id']] = array("parent_id" => $row['parent_id'], "title" => $row['title']);
}

$aCounters = array('0' => 0 , '1' => 0 , '2' => 0, '3' => 0 , '4' => 0 , '5' => 0);
$aCategoryCode = array();

function BuildFirst($array, $currentParent, $currLevel = 0, $prevLevel = -1 , $db) {
	global $aCounters,$aCategoryCode;
	
	foreach ($array as $categoryId => $category) {
	
		if ($currentParent == $category['parent_id']) {
				
			$aCounters[$currLevel]++;
				
			if ($currLevel == 0) {
				$tag = $aCounters[0];
			} else if ($currLevel == 1) {
				$tag = $aCounters[0].'.'.$aCounters[1];
				$aCounters[2] = 0;
				$aCounters[3] = 0;
				$aCounters[4] = 0;
			} else if ($currLevel == 2) {
				$tag = $aCounters[0].'.'.$aCounters[1].'.'.$aCounters[2];
				$aCounters[3] = 0;
				$aCounters[4] = 0;
			} else if ($currLevel == 3) {
				$tag = $aCounters[0].'.'.$aCounters[1].'.'.$aCounters[2].'.'.$aCounters[3];
				$aCounters[4] = 0;
			} else if ($currLevel == 4) {
				$tag = $aCounters[0].'.'.$aCounters[1].'.'.$aCounters[2].'.'.$aCounters[3].'.'.$aCounters[4];
			} else if ($currLevel == 5) {
				$tag = $aCounters[0];
			}
				
			$aCategoryCode[$categoryId] = $tag;
				
	
			if ($currLevel > $prevLevel) {
				$prevLevel = $currLevel;
			}
				
			$dbs = $db->query("SELECT t2.type, t2.data FROM article t1 JOIN article_part t2 ON t1.id = t2.article_id WHERE t1.chapter_id = " . $categoryId);
				
			$articleParts = array();
			if ($dbs->rowCount() <= 0) {
			}
			while(($row = $dbs->fetch(PDO::FETCH_ASSOC)) !== FALSE) {
				$articleParts[$row['id']] = array("parent_id" => $row['parent_id'], "title" => $row['title']);
	
				if ($row['type'] == "img") {
				}
	
				if ($row['type'] == "str") {
				}
	
				if ($row['type'] == "tbl") {
					$rowData = json_decode($row['data']);
					foreach ($rowData as $k => $v) {
					}
				}
	
			}
	
			$currLevel++;
	
			BuildFirst ($array, $categoryId, $currLevel, $prevLevel , $db);
	
			$currLevel--;
		}
	
	}
	
}


function createTreeView($array, $currentParent, $currLevel = 0, $prevLevel = -1 , $db) {
	global $aCounters,$aCategoryCode;
	
	$content = "";
	
	foreach ($array as $categoryId => $category) {

		if ($currentParent == $category['parent_id']) {
			if ($currLevel > $prevLevel) $content .= "";

			if ($currLevel == $prevLevel) $content .= "";
			
			$aCounters[$currLevel]++;
			
			if ($currLevel == 0) {
				$tag = $aCounters[0];
			} else if ($currLevel == 1) {
				$tag = $aCounters[0].'.'.$aCounters[1];
				$aCounters[2] = 0;
				$aCounters[3] = 0;
				$aCounters[4] = 0;
			} else if ($currLevel == 2) {
				$tag = $aCounters[0].'.'.$aCounters[1].'.'.$aCounters[2];
				$aCounters[3] = 0;
				$aCounters[4] = 0;
			} else if ($currLevel == 3) {
				$tag = $aCounters[0].'.'.$aCounters[1].'.'.$aCounters[2].'.'.$aCounters[3];
				$aCounters[4] = 0;
			} else if ($currLevel == 4) {
				$tag = $aCounters[0].'.'.$aCounters[1].'.'.$aCounters[2].'.'.$aCounters[3].'.'.$aCounters[4];
			} else if ($currLevel == 5) {
				$tag = $aCounters[0];
			}
			
			$pre = "";
			for($i = 0; $i < $currLevel; $i++) {
				$pre .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			
			$content .= '<h'.($currLevel + 1).' id="'.$tag.'">'.$pre.$tag.' '.$category['title'].'</h'.($currLevel + 1).'>';

			if ($currLevel > $prevLevel) { 
				$prevLevel = $currLevel; 
			}
			
			$dbs = $db->query("SELECT t2.type, t2.data FROM article t1 JOIN article_part t2 ON t1.id = t2.article_id WHERE t1.chapter_id = " . $categoryId);
			
			$articleParts = array();
			if ($dbs->rowCount() <= 0) {
				//$content .= "<strong>-- NO DATA</strong>";
			}
			while(($row = $dbs->fetch(PDO::FETCH_ASSOC)) !== FALSE) {
				$articleParts[$row['id']] = array("parent_id" => $row['parent_id'], "title" => $row['title']);
				
				if ($row['type'] == "img") {
					$content .= "<div class='row text-center'>";
					$content .= "<img src='assets/appypetv2/images/".$row['data']."' width='60%'>";
					$content .= "</div><br/><br/>";
				}
				
				if ($row['type'] == "str") {
					$content .= "<div class='row str'>";
					$content .= $row['data'];
					$content .= "</div><br/>";
				}
				
				if ($row['type'] == "tbl") {
					$rowData = json_decode($row['data']);
					$content .= "<table class='table table-striped table-bordered'><thead><tr><th>Field</th><th>Type</th><th>Notes</th><th>Criteria</th><th>Link</th></tr></thead><tbody>";
					foreach ($rowData as $k => $v) {
						$content .= "<tr><td class='col-xs-2'>".$v->field."</td><td class='col-xs-2'>".$v->type."</td><td class='col-xs-2'>".$v->notes."</td><td class='col-xs-3'>".$v->criteria."</td><td class='col-xs-3'><a href='#".$aCategoryCode[explode("|",$v->link)[0]]."'>".$aCategoryCode[explode("|",$v->link)[0]]." ".explode("|",$v->link)[1]."</a></td></tr>";
					}
					$content .= "</tbody></table><br/><br/>";
				}
				
				if ($row['type'] == "ctbl") {
					$rowData = json_decode($row['data']);
					
					
					
					$content .= "<table class='table table-striped table-bordered'><thead><tr>";
					$a = explode(",",$rowData->{0});
					foreach($a as $k => $v) {
						$content .= "<th>".$v."</th>";
					}
					
					$content .= "</tr></thead><tbody>";
					
					foreach ($rowData as $k => $v) {
						if ($k > 0) {
							$content .= "<tr>";
							foreach ($v as $k1 => $v1) {
								$content .= "<td>".$v1."</td>";								
							}
							$content .= "</tr>";
							//$content .= "<tr><td class='col-xs-2'>".$v->field."</td><td class='col-xs-2'>".$v->type."</td><td class='col-xs-2'>".$v->notes."</td><td class='col-xs-3'>".$v->criteria."</td><td class='col-xs-3'><a href='#".$aCategoryCode[explode("|",$v->link)[0]]."'>".$aCategoryCode[explode("|",$v->link)[0]]." ".explode("|",$v->link)[1]."</a></td></tr>";
						}
					}
					$content .= "</tbody></table><br/><br/>";
				}
				
				if ($row['type'] == "dtbl") {
					$rowData = json_decode($row['data']);
					$content .= "<table class='table table-striped table-bordered'><thead><tr><th>Field</th><th>Type</th><th>Notes</th><th>Link</th></tr></thead><tbody>";
					foreach ($rowData as $k => $v) {
						$content .= "<tr><td class='col-xs-3'>".$v->field."</td><td class='col-xs-3'>".$v->type."</td><td class='col-xs-3'>".$v->notes."</td><td class='col-xs-3'><a href='#".$aCategoryCode[explode("|",$v->link)[0]]."'>".$aCategoryCode[explode("|",$v->link)[0]]." ".explode("|",$v->link)[1]."</a></td></tr>";
					}
					$content .= "</tbody></table><br/><br/>";
				}
				
			}

			$currLevel++;

			$content .= createTreeView ($array, $categoryId, $currLevel, $prevLevel , $db);

			$currLevel--;
		}

	}

	if ($currLevel == $prevLevel) $content .= " ";
	
	return $content;
	
}

$content = '';

// reference the Dompdf namespace


// instantiate and use the dompdf class
/*$dompdf = new Dompdf();
$dompdf->loadHtml($content);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('AppyPet');*/
echo $content;

?>

<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
		<link rel="stylesheet" href="bootstrap.min.css" crossorigin="anonymous">

		<style>
			ol { counter-reset: item }
			li { display: block }
			
			h0 {
				font-size : 45px;
			}
			
			h1 {
				font-size : 35px;
				color : blue;
			}
			
			h2 {
				font-size : 30px;
				color : blue;
			}
			
			h3 {
				font-size : 28px;
				color : blue;
			}
			
			h4 {
				font-size : 25px;
				color : blue;
			}
			
			h5 {
				font-size : 20px;
				color : blue;
			}
			
			h6 {
				color : blue;
			}
			
			.str {
				margin-left : 2em;
				margin-right : 2em;
			}
			
			.pointer {
				font-weight: bold;
				font-size : 20px;
			}
			
			
		</style>
	
	</head>
	
	<body>

		<div class="container">
		
		<?php 
		$dbs = $db->query("SELECT * FROM document WHERE id = ".$document_id);
		$row = $dbs->fetch(PDO::FETCH_ASSOC);
		echo "<div class='row'><div class='col-xs-12 text-right'><img src='https://scontent-lhr3-1.xx.fbcdn.net/hphotos-xat1/v/t1.0-9/12108029_1040856645966023_8007255984731763979_n.jpg?oh=5885425ca265b5758b30ec41087385a8&oe=5783663C' style='width:100px'></div></div>";
		echo "<div class='row'><div class='col-xs-12 text-center'><h0>".$row['name']."</h0><br/>Functional Specification</div><br/><br/><br/><br/><br/><br/><br/><br/>&nbsp;Author : ".$row['Author']."<br/>&nbsp;Date : ".date("Y-m-d")."<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/></div>";
		
		echo "<h3>Contents</h3>";
		echo "<table width='100%' class='table table-condensed table-striped'><thead><tr><th>Chapter</th><th class='text-right'>Section</th></tr></thead>";
		
		BuildFirst($arrayCategories, 0 , 0 , -1 , $db);
		
		$aOnlyKids = array(8,9,174);
		
		foreach($arrayCategories as $key => $val) {
			if ($val['parent_id'] == 0) {
				echo "<tr><td><a href='#".$aCategoryCode[$key]."'>".$val['title']."</a></td><td width='50%' align='right'><a href='#".$aCategoryCode[$key]."'>".$key."</a></td></tr>";
				if (in_array($key , $aOnlyKids)) {
					
					foreach($arrayCategories as $key2 => $val2) {
						if ($val2['parent_id'] == $key) {
							echo "<tr><td>. . .<a href='#".$aCategoryCode[$key2]."'>".$val2['title']."</a></td><td width='50%' align='right'><a href='#".$aCategoryCode[$key2]."'>".$aCategoryCode[$key2]."</a></td></tr>";
							
							foreach($arrayCategories as $key3 => $val3) {
								if ($val3['parent_id'] == $key2) {
									echo "<tr><td>. . . . . .<a href='#".$aCategoryCode[$key3]."'>".$val3['title']."</a></td><td width='50%' align='right'><a href='#".$aCategoryCode[$key3]."'>".$aCategoryCode[$key3]."</a></td></tr>";
									
									foreach($arrayCategories as $key4 => $val4) {
										if ($val4['parent_id'] == $key3) {
											echo "<tr><td>. . . . . . . . .<a href='#".$aCategoryCode[$key4]."'>".$val4['title']."</a></td><td width='50%' align='right'><a href='#".$aCategoryCode[$key4]."'>".$aCategoryCode[$key4]."</a></td></tr>";
											foreach($arrayCategories as $key5 => $val5) {
												if ($val5['parent_id'] == $key4) {
													echo "<tr><td>. . . . . . . . . . . .<a href='#".$aCategoryCode[$key5]."'>".$val5['title']."</a></td><td width='50%' align='right'><a href='#".$aCategoryCode[$key5]."'>".$aCategoryCode[$key5]."</a></td></tr>";
													foreach($arrayCategories as $key6 => $val6) {
														if ($val6['parent_id'] == $key5) {
															echo "<tr><td>. . . . . . . . . . . . . . .<a href='#".$aCategoryCode[$key6]."'>".$val6['title']."</a></td><td width='50%' align='right'><a href='#".$aCategoryCode[$key6]."'>".$aCategoryCode[$key6]."</a></td></tr>";
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
					
				}
			}
		}
		
		echo "</table>";
		
		
		$aCounters = array('0' => 0 , '1' => 0 , '2' => 0, '3' => 0 , '4' => 0 , '5' => 0);
		echo  createTreeView($arrayCategories, 0 , 0 , -1 , $db) ?>

		</div>
	</body>

</html>


			
		