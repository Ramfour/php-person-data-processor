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
    return $sexChar;
}
