<form method="post">
    <input type="text" name="book" placeholder="book">
    <input type="text" name="author" placeholder="author">
    <button name="add" type="submit">Add</button>
</form>
<?php
require 'config.php';
    if (isset($_POST['add'])){
//        $data['new_author']
        $query = $pdo->prepare('SELECT
          b.title BookTitle,
          a.name_author NameAuthor,
        b.book_id
        FROM author_book ab
          INNER JOIN book b ON b.book_id = ab.book_id
          INNER JOIN author a ON a.author_id = ab.author_id WHERE b.title = :title;');

        $query->bindParam(':title', $_POST['book']);
        $query->execute();
        $row = $query->fetch();
        $send = $pdo->prepare('INSERT INTO author(name_author) VALUES(:author)');
        $send->bindParam(':author', $_POST['author']);
        $send->execute();
        $author_id = $pdo->lastInsertId();

        $send = $pdo->prepare('INSERT INTO author_book VALUES (:author_id, :book_id)');
        $send->bindParam(':book_id', $row['book_id']);
        $send->bindParam(':author_id', $author_id);
        $send->execute();
}