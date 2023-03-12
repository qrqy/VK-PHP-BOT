  <?php
	function sendMessage($message_info){
		$get_params = http_build_query($message_info);
		file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
	}
	if (!isset($_REQUEST)) {
		return;
	}
	//Подключение
	$confirmation_token = 'token';
	$token = 'token';	
	$data = json_decode(file_get_contents('php://input'));
	//Подключение БДшки
	$db= new SQLite3('base.sqlite');
	
	//Пункт для добавления в таблицу столбцов
	$res = $db->exec(
    "CREATE TABLE IF NOT EXISTS statistic (
	  code INTEGER PRIMARY KEY,
	  id INTEGER UNIQUE,
      vip INTEGER,
      counts INTEGER, 
      reputation INTEGER,
	  role TEXT);"
  );
	

	switch ($data->type) {
	case 'confirmation':
		echo $confirmation_token;
		break;

	case 'message_new':
		echo('ok');
		$user_id = $data->object->message->from_id;
		
		
		
		$repl_id=$data->object->message->peer_id; //айди для ответа
		$msg_text=$data->object->message->text;//текст сообщения
		$pieces_msg_text = explode(" ", $msg_text);//делим сообщение чтобы получить разные его части
		$command_name=$pieces_msg_text[0];//получаем первое слово чтобы определить команду
		$command_name=mb_strtolower($command_name);//опускаем регистр чтобы работало любое написание команды
		//Проверяем есть ли в БД пользователь
		

		
		$db->query("INSERT OR IGNORE INTO statistic (code, id, vip, counts, reputation, role) VALUES ({$user_id}, {$user_id}, 0, 0, 0, 'Чачер')");
		//запрашиваем данные о пользователе для дальнейшего использования, например подсчета сообщений
		$profile_info=$db->query("SELECT code, id, vip, counts, reputation, role FROM statistic WHERE id={$user_id}");
		$profile_info_array=$profile_info->fetchArray();
		
		$counts_messages=$profile_info_array['counts']+1;

		$db->query("REPLACE INTO statistic (id, vip, counts, reputation, role) VALUES ({$user_id},{$profile_info_array['vip']}, {$counts_messages}, {$profile_info_array['reputation']}, '{$profile_info_array['role']}')");
		
		if ($command_name==".привет"){
			$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.103"));
			$user_name = $user_info->response[0]->first_name;//получаем имя
			$user_nickname = $user_info->response[0]->domain;//айди пользователя для например тегов
			$request_params = array(
			'message' => "Привет, {$user_name}!",
			'peer_id' => $repl_id,
			'access_token' => $token,
			'v' => '5.131',
			'random_id' => '0'
			);
			sendMessage($request_params);
		} elseif ($command_name==".дата"){
			$month_array=['Января, ', 'Февраля, ', 'Марта, ', 'Апреля, ', 'Мая, ', 'Июня, ', 'Июля, ', 'Августа, ', 'Сентября, ', 'Октября, ', 'Ноября, ', 'Декабря, '];
			$month_number=date('n')-1;
			$days_array=['Воскресенье,', 'Понедельник,', 'Вторник,', 'Среда,', 'Четверг,', 'Пятница,', 'Суббота,'];
			$days_number=date('w');
			$time=date('H:i:s ', time()+10800);
			$number_day=date('j');
			$time .=date(' j');
			if ($number_day==3){
				$time .='-е ';
			} else{
				$time .='-ое ';
			}
			$time .=$month_array[$month_number];
			$time .=$days_array[$days_number];
			$time .=date(' Y');
			$request_params = array(
			'message' => "Текущая дата {$time}",
			'peer_id' => $repl_id,
			'access_token' => $token,
			'v' => '5.131',
			'random_id' => '0'
			);
			sendMessage($request_params);
		}elseif ($command_name==".команды"){
			$request_params = array(
			'message' => "Привет, для удобства все команды написаны в отдельном документе, изучи его перед тем как пользоваться ботом! vk.com/@botkirvi-komandy ",
			'peer_id' => $repl_id,
			'access_token' => $token,
			'v' => '5.131',
			'random_id' => '0'
			);
			sendMessage($request_params);
		}elseif ($command_name==".монета"||$command_name==".монетка"||$command_name==".подбросить"||$command_name==".коин"){
			$rand_number=random_int(0, 1);
			if ($rand_number==0){
				$request_params = array(
				'message' => "Орел",
				'peer_id' => $repl_id,
				'access_token' => $token,
				'v' => '5.131',
				'random_id' => '0'
				);
			}else{
				$request_params = array(
				'message' => "Решка",
				'peer_id' => $repl_id,
				'access_token' => $token,
				'v' => '5.131',
				'random_id' => '0'
				);
			}
			sendMessage($request_params);
		}elseif ($command_name==".профиль"||$command_name==".пользователь"||$command_name==".инфа"){
			$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.103"));
			$user_name = $user_info->response[0]->first_name;//получаем имя
			$user_nickname = $user_info->response[0]->domain;//айди пользователя для например тегов
			$request_params = array(
				'message' => "Профиль пользователя [id{$user_id}|{$user_name}]\nкол-во сообщений: {$profile_info_array['counts']}\nроли: {$profile_info_array['role']}\nуровень репутации: {$profile_info_array['reputation']} ",
				'peer_id' => $repl_id,
				'access_token' => $token,
				'v' => '5.131',
				'random_id' => '0',
				'disable_mentions' => 1
				);
			sendMessage($request_params);
		}elseif ($command_name==".тест"||$command_name==".test"){
			$request_params = array(
				'message' => "Я тут, жив, цел, орёл",
				'peer_id' => $repl_id,
				'access_token' => $token,
				'v' => '5.131',
				'random_id' => '0'
				);
			sendMessage($request_params);
		}elseif($command_name=="+реп"||$command_name=="-реп"){
			$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.103"));
			$user_name = $user_info->response[0]->first_name;//получаем имя
			$user_nickname = $user_info->response[0]->domain;//айди пользователя для например тегов
			if (isset($data->object->message->reply_message->from_id)){
				$repl_user_id=$data->object->message->reply_message->from_id;
				if ($user_id!=$repl_user_id&&$repl_user_id>0){
					$repl_user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$repl_user_id}&access_token={$token}&v=5.103"));
					$repl_user_name = $repl_user_info->response[0]->first_name;
	
					
					$profile_info=$db->query("SELECT code, id, vip, counts, reputation, role FROM statistic WHERE id={$repl_user_id}");
					$profile_info_array=$profile_info->fetchArray();
					if ($command_name[0]=="+"){
						$reputation=$profile_info_array['reputation']+1;
						$request_params = array(
						'message' => "Пользователю [id{$repl_user_id}|{$repl_user_name}] повысили репутацию ({$reputation})",
						'peer_id' => $repl_id,
						'access_token' => $token,
						'v' => '5.131',
						'random_id' => '0',
						'disable_mentions' => 1
						);
					}else {
						$reputation=$profile_info_array['reputation']-1;
						$request_params = array(
						'message' => "Пользователю [id{$repl_user_id}|{$repl_user_name}] понизили репутацию ({$reputation})",
						'peer_id' => $repl_id,
						'access_token' => $token,
						'v' => '5.131',
						'random_id' => '0',
						'disable_mentions' => 1
						);
					}
					$db->query("REPLACE INTO statistic (id, vip, counts, reputation, role) VALUES ({$profile_info_array['id']},{$profile_info_array['vip']}, {$counts_messages}, {$reputation}, '{$profile_info_array['role']}')");
					
				}elseif ($repl_user_id<0){
					$request_params = array(
					'message' => "Чувак, бот не нуждается в репе",
					'peer_id' => $repl_id,
					'access_token' => $token,
					'v' => '5.131',
					'random_id' => '0'
					);
				}else{
					$request_params = array(
					'message' => "Чувак, самому себе репу давать нини",
					'peer_id' => $repl_id,
					'access_token' => $token,
					'v' => '5.131',
					'random_id' => '0'
					);
				}
			}else{
				$request_params = array(
				'message' => "Чтобы дать кому-то репутацию нужно написать челу в ответ.",
				'peer_id' => $repl_id,
				'access_token' => $token,
				'v' => '5.131',
				'random_id' => '0'
				);
			}
			sendMessage($request_params);

		}
		//Закрытие БДшки
		$db->close();
		break;
	
	}
?>