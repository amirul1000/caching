<?php
	$cachefile = $_SERVER['DOCUMENT_ROOT'].'/tutorial/caching/cache/data.html';
	$cachetime = 10;
	
	// Serve from the cache if it is younger than $cachetime
	if (file_exists($cachefile) && filesize($cachefile)>0 && time() - $cachetime < filemtime($cachefile)) {
		////*****************************////
		// Executing From Cache
		////*****************************////
		echo "<h3>Data is from file:</h3><br>";
		include($cachefile);
	}
	else
	{
		////////////content//////////////
		////mysql datbase connection
		$servername = "localhost";
		$username = "root";
		$password = "secret";
		$db = "test";
		
		// Create connection
		$conn = new mysqli($servername, $username, $password,$db);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		$sql = "SELECT * FROM member";
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				$html = $row["description"];
			}
		} else {
			echo "0 results";
		}
		$conn->close();
		///////////////////////////////
		
		// Cache the contents to a file
		$fp = fopen($cachefile, 'w');
		//https://www.php.net/manual/en/function.flock.php
		if(flock($fp,LOCK_EX))  //LOCK_EX to acquire an exclusive lock (writer)
		 {
			fwrite($fp,$html);
			flock($fp,LOCK_UN);//LOCK_UN to release a lock (shared or exclusive).
            fclose($fp);
		 }
		////*****************************////
		// Executing From Main File DB Connected
		////*****************************////
		echo "<h3>Data is from Mysql:</h3><br>";
		echo $html;
	}
?>
