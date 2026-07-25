<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    require __DIR__ . '/../data/arrays.php';
    require __DIR__ . '/../src/helpers/functions.php';
    echo getGenderDescription($examplePersonsArray, $rules);
    /** 
     * @var array $persons {@see arrays.php}
     * @var array $rules {@see arrays.php}
     */
    echo getPerfectPartner('Иванов', 'Иван', 'Иванович', $examplePersonsArray, $rules);
    ?>
</body>

</html>