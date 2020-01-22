<?php
    /**
     * Created to ease the process of displaying table in html
     * 
     * @author Mohinish Sharma
     * @package mHelp
     * @version v1.1.2
     */
    require('inc/mHelp.php');
    use mHelp\mHelp as mHelp;

    // database connection
    $d = array(
        "hostname"=>"localhost",
        "port"=>"3307", // port is opitonal and not used in this implementation
        "username"=>"root",
        "password"=>"",
        "database"=>"staples"
    );

    // Coulmn name and Column display name mapping 
    // => with key as column name and value as column display name
    $c = array(
        "memberID"=>"#",
        "username"=>"Username",
        "password"=>"Password"
    );

    $v = new mHelp($d); // mhelp object

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>mHELP Demo</title>
</head>
<body>
    <?= $v->make_table("members",$c)?>
</body>
</html>
