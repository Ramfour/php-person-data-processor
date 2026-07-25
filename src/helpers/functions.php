<?php

/**
 * Принимает как аргумент три строки — фамилию, имя и отчество. 
 * Возвращает как результат их же, но склеенные через пробел.
 *
 * @param string $surname Фамилия
 * @param string $name Имя
 * @param string $patronymic Отчество
 * @return string Склеенное полное имя
 */
function getFullnameFromParts(string $surname, string $name, string $patronymic): string
{
    return "$surname $name $patronymic";
}

/**
 * Принимает как аргумент полное имя, состоящее из трёх слов. 
 * Возвращает как результат массив, в котором под ключами 'surname', 'name' и 'patronymic' 
 * будут лежать соответствующие части полного имени.
 *
 * @param string $fullname Полное имя
 * @return array Массив с частями полного имени
 */
function getPartsFromFullname(string $fullname): array
{
    $keys = ['surname', 'name', 'patronymic'];
    $values = explode(' ', $fullname);
    return array_combine($keys, $values);
}

/**
 * Принимает как аргумент полное имя, состоящее из трёх слов. 
 * Возвращает как результат строку, состоящую из имени и инициалов.
 *
 * @param string $fullname Полное имя
 * @return string Имя и инициалы фамилии
 */
function getShortName(string $fullname): string
{
    $parts = getPartsFromFullname($fullname);
    return $parts['name'] . ' ' . mb_substr($parts['surname'], 0, 1) . '.';
}

/**
 * Принимает как аргумент полное имя, состоящее из трёх слов. 
 * Возвращает как результат пол человека: 1 — мужчина, -1 — женщина, 0 — пол не определён.
 * @param string $fullname Полное имя
 * @param array $rules Массив правил для определения пола по имени
 * @return int Пол человека
 */
function getGenderFromName(string $fullname, array $rules): int
{
    $parts = getPartsFromFullname($fullname);
    $sexChar = 0;
    foreach ($parts as $key => $value) {
        foreach ($rules[$key] as $gender => $endings) {
            foreach ($endings as $ending) {
                if (mb_substr($value, -mb_strlen($ending)) === $ending) {
                    if ($gender === 'male') {
                        $sexChar++;
                    } elseif ($gender === 'female') {
                        $sexChar--;
                    }
                }
            }
        }
    }
    if ($sexChar > 0) return 1;
    if ($sexChar < 0) return -1;
    return 0;
}
/**
 * Принимает как аргумент массив людей, состоящий из полных имён и должностей. 
 * Возвращает как результат строку с описанием соотношения полов в массиве.
 * @var array $rules Массив правил для определения пола по имени {@see arrays.php}
 * @param array $persons Массив людей
 * @return string Описание соотношения полов
 */
function getGenderDescription(array $persons, array $rules): string
{
    $result = '';
    $personsCount = count($persons);
    $males   = array_filter($persons, fn($p) => getGenderFromName($p['fullname'], $rules) > 0);
    $females = array_filter($persons, fn($p) => getGenderFromName($p['fullname'], $rules) < 0);
    $unknown = array_filter($persons, fn($p) => getGenderFromName($p['fullname'], $rules) === 0);
    $callback = function ($count) use ($personsCount) {
        return round($count / $personsCount * 100, 1);
    };
    $malePercent = $callback(count($males));
    $femalePercent = $callback(count($females));
    $unknownPercent = $callback(count($unknown));
    $result = <<<RESULT
                <pre>
                Гендерный состав аудитории: 
                ---------------------------
                Мужчины - {$malePercent}%
                Женщины - {$femalePercent}%
                Не удалось определить - {$unknownPercent}%
                </pre>
                RESULT;
    return $result;
}

/**
 * Принимает как аргумент строку. 
 * Возвращает как результат строку с первой буквой в верхнем регистре, 
 * а остальными буквами в нижнем регистре.
 *
 * @param string $string Строка
 * @return string Строка с первой буквой в верхнем регистре
 */
function getFirstCharUpper(string $string): string
{
    if ($string === '') {
        return '';
    }
    $firstChar = mb_substr($string, 0, 1, 'UTF-8');
    $restOfString = mb_strtolower(mb_substr($string, 1, null, 'UTF-8'), 'UTF-8');
    return mb_strtoupper($firstChar, 'UTF-8') . $restOfString;
}

/**
 * Принимает как аргумент массив людей, состоящий из полных имён и должностей. 
 * Возвращает как результат случайного человека из массива.
 *
 * @param array $persons Массив людей
 * @return array Случайный человек из массива
 */
function takeRandomPerson(array $persons): array
{
    return $persons[array_rand($persons)];
}

/**
 * Принимает как аргумент фамилию, имя и отчество, массив людей и массив правил для определения пола по имени. 
 * Возвращает как результат строку с идеальной парой.
 *
 * @param string $surname Фамилия
 * @param string $name Имя
 * @param string $patronymic Отчество
 * @param array $persons Массив людей
 * @param array $rules Массив правил для определения пола по имени
 * @return string Строка с идеальной парой
 */
function getPerfectPartner(string $surname, string $name, string $patronymic, array $persons, array $rules): string
{
    $whileCounter = 0;
    $fullname = getFullnameFromParts(
        getFirstCharUpper($surname), 
        getFirstCharUpper($name), 
        getFirstCharUpper($patronymic));
    $gender = getGenderFromName($fullname, $rules);
    while (true) {
        $whileCounter++;
        if ($whileCounter > 1000) {
            return 'Не удалось найти идеальную пару после 1000 попыток.';
        }
        $randomPerson = takeRandomPerson($persons);
        $randomGender = getGenderFromName($randomPerson['fullname'], $rules);
        if ($gender * $randomGender < 0) {
            $firstPersonShortName = getShortName($fullname);
            $secondPersonShortName = getShortName($randomPerson['fullname']);
            $randomPercent = rand(5000, 10000) / 100;
            return $firstPersonShortName . ' + ' . $secondPersonShortName . ' =' . PHP_EOL . '♡ Идеально на ' . $randomPercent . '% ♡';
        }
    }
}
