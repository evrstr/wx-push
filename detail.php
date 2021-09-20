<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $_GET['title'] ?>
    </title>

</head>



<body>
    <h2><?php echo $_GET['title'] ?></h2>
    <p></p>
    <?php echo $_GET['description']; ?>
    <p align="right">by -- <a href="http://yjqz.cc">御驾亲征</a></p>
    <p>官网：<?php echo $_SERVER['HTTP_HOST']; ?></p>

</body>

</html>