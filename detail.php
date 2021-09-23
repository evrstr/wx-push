<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    class MyDB extends SQLite3
    {
        function __construct()
        {
            $this->open('db.db');
        }
    }
    $sql = <<<EOF
SELECT * from msg where id = '{$_REQUEST['id']}';
EOF;
    $db = new MyDB();
    $ret = $db->query($sql);
    $data = null;
    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        $data = $row;
    }
    $db->close();
    ?>
    <title>
        <?php echo $data['title'] ?>
    </title>

</head>

<body>
    <?php echo  html_entity_decode(stripcslashes($data['detail']));
    ?>
</body>

</html>