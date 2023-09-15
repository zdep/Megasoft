<?php
	/**
	 * Задача №3.
	 * Создать скрипт, который в папке /datafiles найдет все файлы, имена которых 
	 * состоят из цифр и букв латинского алфавита, имеют расширение ixt и выведет 
	 * на экран имена этих файлов, упорядоченных по имени.
	 * Задание должно быть выполнено с использованием регулярных выражений.
	 * Весь код должен быть прокомментирован в стиле PHPDocumentor'а.
	 */

	/** Скрипт, так скрипт :) */
	if ($datafiles = realpath('datafiles') === false) {
		exit('Нет доступа к папке /datafiles');
	}

/**
 *	Есть еще вариант обхода папки с использованием opendir и readdir,
 *	но мне они не нравятся, да и кода больше. 
 *	С помощью blob можно искать по маске, хоть и пришлось дописать реплейс.
 *  Так же есть вариант с DirectoryIterator.
 *
 *	if ($h = opendir('.')) {
 *		while (false !== ($f = readdir($h))) {
 *			if ($f != "." && $f != "..") {
 *				...
 *			}
 *		}
 *	}
*/

	foreach (glob('datafiles/*.ixt') as $filename) {
		$f = str_replace('datafiles/', '', $filename);
		if ($a = preg_match('/[a-z0-9]+.ixt/i', $f)) {
			echo $f.'<br>';
		}
	}

	foreach (glob('datafiles/*.ixt') as $filename) {
		$f = str_replace('datafiles/', '', $filename);
		if ($a = preg_match('/[a-z0-9]+.ixt/i', $f)) {
			echo $f.'<br>';
		}
	}

/**
 *	В функции blob, имена файлов сортируются по алфавиту, поэтому нет надобности в
 *	дополнительной сортировке. Но вдруг, надо отсортировать по-другому, тогда код
 *	будет примерно такой:
 *
 *	$files = [];
 *	foreach (glob('datafiles/*.ixt') as $filename) {
 *		$f = str_replace('datafiles/', '', $filename);
 *		if ($a = preg_match('/[a-z0-9]+.ixt/i', $f)) {
 *		$files[] = $f;
 *		}
 *	}
 *
 *	rsort($files);
 *
 *	echo implode('<br>', $files);
*/
