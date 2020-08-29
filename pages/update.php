<?php
require 'config.php';
$data = $_POST;
$sql = 'SELECT DISTINCT
  b.title BookTitle,
                a.name_author NameAuthor
FROM author_book ab
  INNER JOIN book b ON b.book_id = ab.book_id
  INNER JOIN author a ON a.author_id = ab.author_id';
$query = $pdo->query("$sql");

while ($row = $query->fetch()) {
    echo $row['BookTitle'] . ' - ' . $row['NameAuthor'] . '<br>';
}
?>
    <br>
    <form action="?pages=update" method="post">
        <label>Детализация по книге</label> <input type="text" name="book_title" placeholder="Введите книгу">
        <button type="submit" name="submit">Update</button>
    </form>
    <br>
<?php
if (isset($data['submit'])) {
    echo 'Редактируем книгу - ' . $data['book_title'] . '<br>';
    $book = $data['book_title'];
//        var_dump($book);
    $query = $pdo->prepare('SELECT
  b.title BookTitle,
  a.name_author NameAuthor
FROM author_book ab
  INNER JOIN book b ON b.book_id = ab.book_id
  INNER JOIN author a ON a.author_id = ab.author_id WHERE b.title = :title;');
    $query->bindParam(':title', $book);
    $query->execute();
    while ($row = $query->fetch()) {
        echo 'В создание книги участвовал: ' . $row['NameAuthor'] . '<br>';
    }
}
$lang = $pdo->prepare('SELECT
  b.title BookTitle,
  l.lang Lang
FROM book_lang bl
  INNER JOIN book b ON b.book_id = bl.book_id
  INNER JOIN lang l ON l.lang_id = bl.lang_id
   WHERE b.title = :title;');
$lang->bindParam(':title', $book);
$lang->execute();

while ($value = $lang->fetch()) {
    echo 'Язык книги: ' . $value['Lang'] . '<br>';
}
?>
    <form method="post">
        <button type="submit" name="add_author">Добавить автора</button>
        <button type="submit" name="add_lang">Добавить язык</button>
    </form>
<?php
    if (isset($data['add_author'])) {
        ?>
        <a href="?pages=new_author">Добавить еще одного автора</a>
        <?php
    }
    if (isset($data['add_lang'])) {
        ?>
        <a href="?pages=new_lang">Добавить еще один язык</a>
        <?php
    }