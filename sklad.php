<?php
require("lib.php");
if(isset($_COOKIE["user"])){
	if(!logged($_COOKIE["user"])){
		header("Location: login.php");
		prodlouzit("id");
		exit;
	}
}
else {
	header("Location: login.php");
	exit;
}

if(isset($_POST["id"])){
	if(puvodni($_POST["id"])){
		header("Content-Disposition: attachement; filename='".$_POST["id"]."'");
		readfile("./sklad/".$_POST["id"]);
	}
};

if(isset($_POST["toDel"])){
	if(puvodni($_POST["toDel"])){
		deleteFile($_POST["toDel"]);
	}
};

date_default_timezone_set("Europe/Prague");
$pole_zaloh = getDirArray("sklad/105110102111", false);
?>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="javascript/prefixfree.min.js"></script>
	<script src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
	<script src="./javascript/game_of_life.js"></script>
	<script>
	$(document).on("keyup",function(e){
		if(e.keyCode == 121){
			setInterval( function(){
				bg.update(1);
				bg.render();
			}, 133 )
		}
	});
	$(document).ready(function(){
		bg = new GOLBackground( document.body, 256, 256, 6, 2);
		bg.update(20);
		bg.render();
		$(".filtering .tags").on("keyup",function(){
			if($(this).attr("value") == ""){
				$(".file").css("display","block")
				return;
			}
			var search = $(this).attr("value").split(" ");
			
			$(".file").each(function(){
				$(this).css("display","none");
				var tags = $(this).data("tags").split(" ");
				for(var i in search){
					for(var tag in tags){
						if(tags[tag].indexOf(search[i]) >= 0){
							$(this).css("display","block");
						}
					}
				}
			})
		});
	});
	
	function trySubmit(form){
		if(confirm("Opravdu smazat "+form.children[0].value+"?"))
			form.submit();
	};
	</script>
	<title>Skladiště</title>
</head>
<body>
	<?php
	include("header.php");
	?>
	<div class="filtering">
		Filtrovat podle tagů: <input class="tags" type="text" placeholder="modely, dolni-patra, budova">
	</div>
	<?php 
	if($pole_zaloh){
		for($i=count($pole_zaloh)-1;$i>=0;$i--){
			if(getTask($pole_zaloh[$i]["link"], "tasks.txt")["name"])
				$linktask = getTask($pole_zaloh[$i]["link"], "tasks.txt")["name"];
			else $linktask = "Příloha není vázána";
			// přidat src a tags
			echo "<div class='file' data-src='". $pole_zaloh[$i]["download"] ."' data-tags='".$pole_zaloh[$i]["tags"]."'>
				<img class='icon' src='http://www.stdicon.com/".$pole_zaloh[$i]["download"]."?size=92&default=http://www.stdicon.com/application/octet-stream'>
				<span class='name'>".$pole_zaloh[$i]["name"]."</span>
				<span class='tags'><span class='important'>Tagy:</span> ".implode(", ", explode(" ", $pole_zaloh[$i]["tags"]))."</span><br>
				<span class='date'><span class='important'>Přidáno</span> ".date("d.m.Y G:i",intval($pole_zaloh[$i]["date"]))."</span>
				<span class='download'>
				";
		
			if(substr($pole_zaloh[$i]["download"],0,7) != "http://"){
				echo "<form method='post' action='sklad.php'>
					<input type='hidden' value='".$pole_zaloh[$i]["download"]."' name='id'>
					<input type='submit' class='odkaz' value='Stáhnout'>
				</form>";
			}
			else{
				echo "<a href='".$pole_zaloh[$i]["download"]."'>Odkaz</a>";
			}
			echo "</span><div onclick='trySubmit(this.children[0]);'><form method='post' action='sklad.php' style='position: absolute; left: 80%;top: 20%;'>
				<input type='hidden' value='".$pole_zaloh[$i]["download"]."' name='toDel'>
				<input type='button' class='odkaz' value='Smazat'>
				</form></div>";
			echo	"
				<div class='moreinfo'>
					<br>
					<span><span class='important'>Důležitost:</span> ".$pole_zaloh[$i]["importancy"]."</span>
					<span><span class='important'>Úkol:</span> ".$linktask."</span><br>
					<span class='description'>".$pole_zaloh[$i]["description"]."</span>
				</div>
				</div>";
			}
	}
	else echo "nejsou k dispozici žádné materiály";
	?>
</body>
</html>