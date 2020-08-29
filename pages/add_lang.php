<form method="post" action="?pages=add_lang">
    <p>Добавить еще один язык:</p>
    <input type="text" name="add_lang" placeholder="Language">
    <input type="submit" name="new_lang" value="Submit">
</form>
<a href="?pages=add_author">Добавить еще автора?</a>
<a href="?pages=addbook">Добавить еще одну книгу</a>
<?php
require 'config.php';
unset($data);
$data = $_POST;
if (isset($data['new_lang'])) {
    $sql = 'SELECT MAX(book_id) FROM book';
    $ans = $pdo->query("$sql");
    $output = $ans->fetch();

    $send = $pdo->prepare('INSERT INTO lang(lang) VALUES(:lang)');
    $send->bindParam(':lang', $data['add_lang']);
    $send->execute();
    $lang_id = $pdo->lastInsertId();

    $query = $pdo->prepare('INSERT INTO book_lang VALUES (:book_id, :lang_id)');
    $query->bindParam(':book_id', $output['0']);
    $query->bindParam(':lang_id', $lang_id);
    $query->execute();

}
