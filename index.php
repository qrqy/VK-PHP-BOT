<html>
  <head>
    <title>БДшка</title>
	  <style type="text/css">
	   TABLE {
	    border-collapse: collapse; /* Убираем двойные границы между ячейками */
	    border: 4px solid #000; /* Рамка вокруг таблицы */
	   }
	   TD, TH {
	    padding: 5px; /* Поля вокруг текста */
	    border: 2px solid green; /* Рамка вокруг ячеек */
	   }
	  </style>
  </head>
  <body>
    <?= '<h1>Профили</h1>'; ?>
    
    <?php  
		$token = '52f853b39308be33fc5821b22ffcc7a3a58aabc2abd7ef760e0a6aea406afadaf9a44ab109981a9387ff1';	
		$db= new SQLite3('base.sqlite');
  		//$db->query("ALTER TABLE statistic ADD COLUMN unix_time INTEGER");
  
  		$results = $db->query("SELECT * FROM statistic");
  		$data=array();
		while ($res= $results->fetchArray()){
		array_push($data, $res);
		}
		//echo json_encode($data);

		$row=array();
		echo '<table border="1"><tr><td>ID пользователя</td><td>Кол-во сооб</td><td>Репка</td><td>Роль</td></tr>';
		while($res = $results->fetchArray()){

            if(!isset($res['id'])) continue;

            $row[$i]['id'] = $res['id'];
            $row[$i]['counts'] = $res['counts'];
            $row[$i]['vip'] = $res['vip'];
			$row[$i]['reputation']=$res['reputation'];
			$row[$i]['role']=$res['role'];

			echo '<tr><td>';
			echo $row[$i]['id'];
			echo '</td><td>';
			echo $row[$i]['counts'];
			echo '</td><td>';
			echo $row[$i]['reputation'];
			echo '</td><td>';
			echo $row[$i]['role'];
			echo '</td></tr>';
            $i++;
          }	
		echo '</table>';
		echo '<h1>Топ по сообщениям</h1>';
		function cmp_counts($a, $b)
		{
		    return $a['counts']<=>$b['counts'];
		}
		usort($row,'cmp_counts');
		echo '<table border="1"><tr><td>ID пользователя</td><td>Кол-во сооб</td><td>Репка</td><td>Роль</td></tr>';
		for ($a=$i-1; $a>=0; $a--){
			echo '<tr><td>';
			echo $row[$a]['id'];
			echo '</td><td>';
			echo $row[$a]['counts'];
			echo '</td><td>';
			echo $row[$a]['reputation'];
			echo '</td><td>';
			echo $row[$a]['role'];
			echo '</td></tr>';
		}
		echo '</table>';
		echo '<h1>Топ по репе</h1>';
		function cmp_rep($a, $b)
		{
		    return $a['reputation']<=>$b['reputation'];
		}
		usort($row,'cmp_rep');
		echo '<table border="1"><tr><td>ID пользователя</td><td>Кол-во сооб</td><td>Репка</td><td>Роль</td></tr>';
		for ($a=$i-1; $a>=0; $a--){
			echo '<tr><td>';
			echo $row[$a]['id'];
			echo '</td><td>';
			echo $row[$a]['counts'];
			echo '</td><td>';
			echo $row[$a]['reputation'];
			echo '</td><td>';
			echo $row[$a]['role'];
			echo '</td></tr>';
		}
		echo '</table>';
?>
  </body>
</html>