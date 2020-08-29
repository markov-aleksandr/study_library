<form method="post" action="?pages=add_author">
    <p>Добавить еще одного автора:</p>
    <input type="text" name="add_author">
    <input type="submit" name="new_author" value="Submit">
</form>
<a href="?pages=add_lang">Добавить еще язык?</a>
<a href="?pages=addbook">Добавить еще одну книгу</a>
<?php
require 'config.php';
unset($data);
$data = $_POST;
if (isset($data['new_author'])) {
$sql = 'SELECT MAX(book_id) FROM book';
$ans = $pdo->query("$sql");
$output = $ans->fetch();

$send = $pdo->prepare('INSERT INTO author(name_author) VALUES(:author)');
$send->bindParam(':author', $data['add_author']);
$send->execute();
$author_id = $pdo->lastInsertId();

$query = $pdo->prepare('INSERT INTO author_book VALUES (:author_id, :book_id)');
$query->bindParam(':author_id', $author_id);
$query->bindParam(':book_id', $output['0']);
$query->execute();
}
