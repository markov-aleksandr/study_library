<form method="post">
    <input type="text" name="book" placeholder="book">
    <input type="text" name="lang" placeholder="lang">
    <button name="add" type="submit">Add</button>
</form>
<?php
require 'config.php';
if (isset($_POST['add'])) {
    $query = $pdo->prepare('SELECT
  b.title BookTitle,
  l.lang Lang,
    b.book_id
FROM book_lang bl
  INNER JOIN book b ON b.book_id = bl.book_id
  INNER JOIN lang l ON l.lang_id = bl.lang_id WHERE b.title = :title');
    $query->bindParam(':title', $_POST['book']);
    $query->execute();
    $row = $query->fetch();
    $send = $pdo->prepare('INSERT INTO lang(lang) VALUES(:lang)');
    $send->bindParam(':lang', $_POST['lang']);
    $send->execute();
    $lang_id = $pdo->lastInsertId();

    $send = $pdo->prepare('INSERT INTO book_lang VALUES (:book_id, :lang_id)');
    $send->bindParam(':book_id', $row['book_id']);
    $send->bindParam(':lang_id', $lang_id);
    $send->execute();
}