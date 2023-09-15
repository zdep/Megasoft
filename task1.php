<?php
/**
 * Задача 1. Написать класс init, от которого нельзя сделать наследника...
 * https://megasoft.ru/tests/phpdeveloper/
 */

/** Сам класс */
final class Init
{
	/** Ресурс PDO */
	private $db = null;

	/** 
	 * Конструктор класса. Подключается к БД
	 * @return void
	 */
	public function __construct()
	{
		$this->db = new PDO("mysql:host=localhost;dbname=test", "root", "root");
		$this->create();
		$this->fill();
	}

	/** 
	 * Создает таблицу `test`
	 * @return void
	 */
	private function create()
	{
        $query = $this->db->prepare(
			"drop table if exists `test`;
			create table `test` (
				`id` int(10) auto_increment,
				`script_name` varchar(25),
				`start_time` bigint,
				`end_time` bigint,
				`result` enum('normal','illegal','failed','success'),
				primary key (`id`) using btree
			);"
		);

        $query->execute();
	}

	/** 
	 * Заполняет таблицу `test` случайными данными
	 * @return void
	 */
	private function fill()
	{
		//foreach (range(0, 100) as $v) {
		for ($i = 0; $i < 100; $i++) {
			$query = $this->db->prepare(
				"insert into test set
				script_name = ?,
				start_time = ?,
				end_time = ?,
				result = ?"
			);

			$start_time = mt_rand(time() - 1e6, time() + 1e6);

			$query->execute([
				substr(bin2hex(random_bytes(mt_rand(1, 50))), 0, 25),
				$start_time,
				$start_time + mt_rand(0, 1e6),
				mt_rand(1, 4),
			]);
		}
	}

	/** 
	 * Выбирает из таблицы test, данные по критерию:
	 * result среди значений 'normal' и 'success'
	 * @return array()
	 */
	public function get()
	{
		$query = $this->db->prepare(
			"select * 
			from test 
			where result in (1, 4)"
		);

		$query->execute();

        return $query->fetchAll(PDO::FETCH_OBJ);
	}
}