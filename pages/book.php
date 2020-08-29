<?php
require 'config.php';
$sql = 'SELECT
    a.name_author NameAuthor,
    count(b.title) AS Количество
FROM author_book ab
         INNER JOIN book b ON b.book_id = ab.book_id
         INNER JOIN author a ON a.author_id = ab.author_id
GROUP BY a.name_author  
ORDER BY `Количество`  DESC;
';
$query = $pdo->query("$sql");
$query->execute();
//echo '<pre>';
//var_dump($row = $query->fetch());
while ($row = $query->fetch()){
    echo '<p style="font-size: 20px; font-weight: bolder; font-family: \'Open Sans\'">'.$row['NameAuthor'].' - ' . $row['Количество']. '</p>';
//    echo $row['Количество'].'</p>';
}
