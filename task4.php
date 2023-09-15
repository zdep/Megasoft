<?php
	/**
	 * Задача №4.
	 * Написать скрипт закачивания страницы www.bills.ru, из страницы извлечь 
	 * даты, заголовки, ссылки в блоке "события на долговом рынке", сохранить в 
	 * таблицу bills_ru_events
	 * Весь код должен быть прокомментирован в стиле PHPDocumentor'а.
	 */

	/** Скрипт, так скрипт :) */
	$file = get('https://www.bills.ru/');
	$dom = new DOMDocument();
	libxml_use_internal_errors(true);
	$dom->loadHTML($file);
	libxml_clear_errors();

	/** тут будут собираться данные :) */
	$table = [];

	/** находим по id необходимую таблицу */
	$item = $dom->getElementById('bizon_api_news_list');

	/** разбиваем на строки */
	$tr = $item->getElementsByTagName('tr');
	foreach ($tr as $v) {
		/** на столбцы */
		$td = $v->getElementsByTagName('td');
		$date = '';
		foreach ($td as $v2) {
			/** на ссылки */
			$a = $v2->getElementsByTagName('a');

			/** а ссылка есть? */
			if ($a->length == 0) {
				/** неа, значит это дата */
				$date = trim($v2->textContent);
			} else {
				/** есть, радостно заполняем :) */
				foreach ($a as $v3) {
					$table[] = [
						'date' => correctDate($date),
						'title' => trim($v3->nodeValue),
						'url' => trim($v3->getAttribute('href')),
					];
				}
			}
		}
	}

	if (!count($table)) {
		exit('Ничего не найдено!');
	}

	/** подключаем БД */
	$db = new PDO('mysql:host=localhost;dbname=test', 'root', 'root');

	/** пересоздаём таблицу */
	$query = $db->prepare(
		'drop table if exists bills_ru_events;
		create table bills_ru_events (
			id int auto_increment,
			date datetime,
			title varchar(230),
			url varchar(240),
			primary key (id) using btree,
			unique index un (url) using btree
		);'
	);

	$query->execute();

	/** заполняем данными */
	foreach ($table as $v) {
		$query = $db->prepare(
			'insert into bills_ru_events set
			date = ?,
			title = ?,
			url = ?'
		);

		$query->execute([
			$v['date'],
			$v['title'],
			$v['url'],
		]);
	}

	/**
	 * Чтение сайта
	 * @param string $url Урл сайта
	 * @return string
	 */
	function get($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Hacker');
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/**
	 * Корректировка даты новости
	 * @param string $date Дата с сайта
	 * @return string|null
	 */
	function correctDate($date)
	{
		return str_replace(
			[
				'янв', 'фев', 'мар', 'апр', 'май', 'июн', 
				'июл', 'авг', 'сен', 'окт', 'ноя', 'дек', ' '
			],
			[
				'01', '02', '03', '04', '05', '06', 
				'07', '08', '09', '10', '11', '12', '.'
			],
			$date
		) ?: null;
	}
