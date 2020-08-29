<form action="?pages=addbook" method="post">
    <label>Enter title</label>
    <input type="text" placeholder="Enter book title" name="book_title"> <br> <br>
    <label>Enter author</label>
    <input type="text" placeholder="Enter author" name="author"> <br><br>
    <label>Enter language</label>
    <input type="text" placeholder="Enter language" name="lang"><br><br>
    <button type="submit" name="submit">Submit</button>
    <hr>


</form>


<?php
require 'config.php';
$data = $_POST;
$sql = 'SELECT title FROM book WHERE title = :title';
$stmt = $pdo->prepare("$sql");
$stmt->bindParam(':title', $data['book_title']);
$stmt->execute();
$row = $stmt->fetch();
//
if (isset($data['submit'])) {
    if ($data['book_title'] == $row['title']) {
        echo "Такая книга уже есть в базе, хотите ее редактировать?";
    } elseif (($data['book_title'] != $row['title'])) {
//         (($data['book_title'] != $row['title']))
        $send = $pdo->prepare('INSERT INTO author(name_author) VALUES(:author)');
        $send->bindParam(':author', $data['author']);
        $send->execute();
        $author_id = $pdo->lastInsertId();

        $send = $pdo->prepare('INSERT INTO book(title) VALUES(:title)');
        $send->bindParam(':title', $data['book_title']);
        $send->execute();
        $book_id = $pdo->lastInsertId();

        $send = $pdo->prepare('INSERT INTO author_book VALUES (:author_id, :book_id)');
        $send->bindParam(':book_id', $book_id);
        $send->bindParam(':author_id', $author_id);
        $send->execute();

        $send = $pdo->prepare('INSERT INTO lang(lang) VALUES(:lang)');
        $send->bindParam(':lang', $data['lang']);
        $send->execute();
        $lang_id = $pdo->lastInsertId();

        $query = $pdo->prepare('INSERT INTO book_lang VALUES (:book_id, :lang_id)');
        $query->bindParam(':book_id', $book_id);
        $query->bindParam(':lang_id', $lang_id);
        $query->execute();


        ?>
        <form method="post" action="?pages=add_author">
            <p>Добавить еще одного автора:</p>
            <input type="text" name="add_author" placeholder="Author:">
            <input type="submit" name="new_author" value="Submit">
            <br><br>
        </form>
            <form method="post" action="?pages=add_lang">
                <p>Добавить еще один язык:</p>
                <input type="text" name="add_lang" placeholder="Language">
                <input type="submit" name="new_lang" value="Submit">
            </form>
        <?php
    }
}

