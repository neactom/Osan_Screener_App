<html>
<head>
<title>Osan AB COVID Gate Questionnaire</title>
<meta description" content="Expedited gate COVID screening survey to help alleviate traffic backups at gates during peak hours in HPCON C and above.">
<meta name="keywords" content="Osan, Osan AB, COVID, COVID-19, Gate, Traffic">
<meta name="author" content="Jeremy Francona">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet"> 
	<style type="text/css">
    body{
      font-family:'Roboto', serif;
      font-size: 20px;
	}
</style>
</head>
<body>
	<center>
    <script LANGUAGE='JavaScript'>
	window.alert('Survey is valid for two hours after taking, or until use. After either of those conditions, you will be required to re-accomplish the survey.');
</script>
<?php
date_default_timezone_set('Asia/Seoul');
$i2d = $_SERVER['REMOTE_ADDR'];
include('config.php');
//include('config2.php');
$currtime = date('Y-m-d H:i:s');
$hot=mysqli_query($conn,"SELECT * FROM hotspot_track ORDER BY DTG DESC");
$htsp = mysqli_fetch_assoc($hot);
$url = $htsp['name'];
$date=strtoupper(date("dMY"));

function ID_GEN()
{
	global $i2d;
	global $date;
	$remove = preg_replace("/[^a-zA-Z0-9]/", "", $i2d);
	$password=array();
	$lib=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","1","2","3","4","5","6","7","8","9");
	$passrand=0;
	while($passrand < 8){
		$spin=rand(0,34);
		array_push($password,$lib[$spin]);
		$passrand++;
	}
	$ipassword = implode('',$password);
	$ID = strtoupper($remove.$date.$ipassword);
	return $ID;	

}

function QR_GEN($ID,$TTPLUS)
{
	global $DDID;
	echo "<font size='5'><b> \n";
	echo "<center> \n";
	echo "THIS QR CODE IS VALID UNTIL: ".$TTPLUS;
	echo "<br> \n";
	echo "<img src='QR.php?id=".$ID."' width='200' height='200'> \n";
	echo "<br> \n";
	echo "Phone: ".$DDID." \n";
	echo "<br> \n";
	echo "SCREENSHOT THIS AND PRESENT TO HEALTH SCREENER AT GATE WITH YOUR ID CARD \n";	
	echo "</center> \n";
	echo "</b></font> \n";
}

function insert_new($conn,$ENT,$ans,$legal,$dte,$ip)
{
	global $currtime;
	$plus = date('Y-m-d H:i:s', strtotime('+2 hour', strtotime($currtime)));
	$inset="INSERT INTO $dte (R_ID,Q,TTIME,VTIME,IPAD,valid,legal) VALUES ('$ENT',$ans,CURRENT_TIMESTAMP,'$plus','$ip','1',$legal)";
	if(mysqli_query($conn,$inset)){
		$il="INSERT INTO overall(R_ID,DATT,TTAKE,valid) VALUES ('$ENT','$dte',CURRENT_TIMESTAMP,'1')";
		if(mysqli_query($conn,$il)){
			QR_GEN($ENT,$plus);
		}
		return true;
	}else{
		return false;
	}
}

function create_new($conn, $dms)
{
	$pp = "CREATE TABLE $dms (CNTR_ID INT(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY,R_ID VARCHAR(40),Q INT(60),TTIME DATETIME,VTIME DATETIME,IPAD VARCHAR(20),scanned DATETIME,valid INT(1),legal INT(1))";
	if(mysqli_query($conn,$pp)){
		return true;
	}else{
		exit;
	}
}



if(isset($_POST['submit'])){
	$temp = $_POST['Qs'];
	$start = 1;
	while($start <= $_POST['Qs']){
		$temp = $temp.$_POST["Q".$start];
		$start++;
	}
	$ans = $temp;
	$DDID = $_POST['DODID'];
	$legal = $_POST['legal'];
	$RID = ID_GEN();
	if(insert_new($conn,$RID,$ans,$legal,$date,$i2d)){
		exit;
	}else{
		create_new($conn,$date);
		insert_new($conn,$RID,$ans,$legal,$date,$i2d);
		exit;
	}

}
echo "<br \n";
?>
<center>
<font size="3">Osan AB COVID Health Questionnaire<br> <?php echo date("l d-M-Y")?></font>
<br>&nbsp;
<font size="5"><b>Providing false information is punishable by either Article 107 of the UCMJ or barment from Osan Air Base.</b></font>
<br>&nbsp;
<form method="post" action="">
<input type="hidden" name="Qs" value="5">
<fieldset style="width:250px;">
<legend><b><font size="5" color="red">MANDATORY</b></font></legend>
<font size="3">Last Four of Phone#:</font>
<input type="text" minlength="4" maxlength="4" name="DODID" size="10" required><br><p>
<font size="4">Q1: <b>Have you traveled out of the country in the last 14 days?</b></font><br>
<input type="radio" name="Q1" value="0" required>No
<input type="radio" name="Q1" value="1" required>Yes<br><p>
<font size="4">Q2: <b>Have you been in contact with someone who traveled out of the country in the last 14 days?</b></font><br>
<input type="radio" name="Q2" value="1" required>Yes
<input type="radio" name="Q2" value="0" required>No<br><p>
<font size="4">Q3: <b>Have you been to any of the USFK hotspots depicted below in the last 14 days?</b></font><br>
<iframe src="https://www.google.com/maps/d/embed?mid=1iX6S9c_0AAJqWSDlqMVxxHBrvd23QEbF&ll=36.00902937501239%2C126.89365796165188&z=6" width="250" height="480"></iframe>
<br>
<input type="radio" name="Q3" value="1" required>Yes
<input type="radio" name="Q3" value="0" required>No<br>
<p>
<font size="4">Q4: <b>Have you had contact with a confirmed positive COVID-19 individual or have you tested positive for COVID-19?</b></font><br>
<input type="radio" name="Q4" value="0" required>No
<input type="radio" name="Q4" value="1" required>Yes<br><p>
<fieldset style="width:250px;">
<legend><font size="4">Q5 - <b>Symptoms - Do you have any of the following?</b></font></legend>
<font color="red" size="2">
<b>Fever of 99.5F/37.5C or greater or Chills<br>
Cough in the last 14 days<br>
Shortness of breath or difficulty breathing<br>
Fatigue<br>
Muscle or body aches<br>
Headache<br>
New loss of sense in taste/smell<br>
Sore Throat<br>
Congestion or runny nose<br>
Nausea/Vomiting/Diarrhea<br>
Repeated Shaking/Chills<br></b>
</font>
<p>
<input type="radio" name="Q5" value="1" required>Yes
<input type="radio" name="Q5" value="0" required>No<br>
</fieldset>
<br>
<center>
<input type="checkbox" id="legal" name="legal" value="1" required><font size="3"><b>By checking this box, you affirm all answers are accurate and truthful</b></font>
<br>

<input type="submit" name="submit">
<input type="reset">
<br>
<font size="2">© apolloscan.com 2020
<br>
<a href="mailto:admin@apolloscan.com">admin@apolloscan.com</a>
</font>

</form>
</center>
</fieldset>
</body>
</html>
