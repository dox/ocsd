<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/ocsd/engine/initialise.php');

$address = Addresses::find_by_uid($_GET['uid']);
?>


<script type="text/javascript">
function PrintWindow()
        {                     
           window.print();            
           CheckWindowState(); 
        }
        
        function CheckWindowState()
        {            
            if(document.readyState=="complete")
            {
                window.close();  
            }
            else
            {            
                setTimeout("CheckWindowState()", 2000)
            }
        }    
        
       PrintWindow();
</script>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Test</title>
	<style type="text/css">
    body {
    	padding-top: 0px;
    	padding-bottom: 0px;
    }
    p {
	    font-family:"Verdana",Georgia,Serif;
	    font-weight: bold;
    }
    </style>
</head>

<p>
<?php
	$output = "";
	
	if ($address->line1) {
		$output .= $address->line1 . "<br />";
    }
    if ($address->line2) {
    	$output .= $address->line2 . "<br />";
    }
    if ($address->line3) {
    	$output .= $address->line3 . "<br />";
    }
    if ($address->line4) {
    	$output .= $address->line4 . "<br />";
    }
    if ($address->town) {
    	$output .= $address->town;
    }
    if ($address->county) {
    	$output .= ", " . $address->county . "<br />";
    }
    if ($address->postcode) {
    	$output .= $address->postcode . "<br />";
    }
    echo $output;
?>
</p>
<body>
</body>
</html>