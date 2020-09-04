<?php
require 'config.php';
$data = $_POST;
$sql = 'SELECT b.book_id, b.title, GROUP_CONCAT(a.name_author SEPARATOR \', \') AS Authors
        FROM book b 
            INNER JOIN author_book ab
            ON b.book_id = ab.book_id
            INNER JOIN author a
            ON ab.author_id = a.author_id
        GROUP BY b.title';
$query = $pdo->query($sql);
?>
    <form action="update.php" method="post">
    <h1>Какую книгу будем сейчас редактировать?</h1>
    <select name="book_title">
        <?php
        $i = 0;
        while ($row = $query->fetch()) {
            echo '<option>' . $row['title'] . '</option>';
        }
        ?>
    </select>
    <button type="submit" name="edit">Редактировать</button>

<?php
if (isset($data['edit'])) {
    // Вывод самой книги.
    echo '<h2> Книга: ' . $data['book_title'], '</h2>';

    // Вывод Авторов книги.
    $show_author = $pdo->prepare('SELECT b.book_id, b.title, GROUP_CONCAT(a.name_author SEPARATOR \', \') AS Authors
        FROM book b 
            INNER JOIN author_book ab
            ON b.book_id = ab.book_id
            INNER JOIN author a
            ON ab.author_id = a.author_id
            WHERE b.title = :title
        GROUP BY b.title ');
    $show_author->bindParam(':title', $data['book_title']);
    $show_author->execute();
    foreach ($show_author as $author) {
        echo 'Авторы: ' . $author['Authors'] . '<br>';
    }

    // Вывод языков книги.
    $show_lang = $pdo->prepare('SELECT b.title, GROUP_CONCAT(l.lang SEPARATOR \', \') AS lang
        FROM book_lang bl
  INNER JOIN book b ON b.book_id = bl.book_id
  INNER JOIN lang l ON l.lang_id = bl.lang_id
            WHERE b.title = :title
        GROUP BY b.title ');
    $show_lang->bindParam(':title', $data['book_title']);
    $show_lang->execute();
    foreach ($show_lang as $language) {
        echo 'Языки: ' . $language['lang'] . '<br>';
    }

    ?>
    <!-- Добавление кнопки и инпута для изменения названия книги.        -->
    <input type="text" name="rename" value="<?php echo @$data['book_title']; ?>">
    <button type="submit" name="edit_name">Редактировать название книги</button>
    <hr>
    <!-- Добавление кнопки и инпута для нового автора.   -->
    <input type="text" name="author" placeholder="Автор">
    <button type="submit" name="add_author">Добавить автора</button>
    <hr>
    <!-- Добавление кнопки и инпута для нового языка.   -->
    <input type="text" name="language" placeholder="Язык">
    <button type="submit" name="add_lang">Добавить язык</button>
    <hr>
    <!-- Добавление кнопки и инпута для удаления автора.   -->
    <input type="text" name="unnecessary_author" placeholder="Удалить автора">
    <button type="submit" name="remove_author">Удалить автора</button>
    <hr>
    <!-- Добавление кнопки и инпута для удаления языка.   -->
    <input type="text" name="unnecessary_language" placeholder="Удалить язык">
    <button type="submit" name="remove_language">Удалить язык</button>
    <hr>
    <!-- Добавление кнопки удаления языка.   -->
    <button type="submit" name="remove_book">Удалить книгу</button>
    </form>
    <?php


} elseif (isset($data['edit_name'])) { // Редактирование названия.
    $rename = $pdo->prepare('UPDATE book SET title = :new_title WHERE title = :book');
    $rename->bindParam(':book', $_POST['book_title']);
    $rename->bindParam(':new_title', $_POST['rename']);
    $rename->execute();
    echo '<br>' . "Редактирование прошло успешно" . '<br>';
} elseif (isset($data['add_author'])) { // Добавление новых авторов.
    $book = $pdo->prepare('SELECT
    DISTINCT  b.book_id
FROM author_book ab
         INNER JOIN book b ON b.book_id = ab.book_id
         INNER JOIN author a ON a.author_id = ab.author_id
WHERE b.title = :title');
    $book->bindParam(':title', $data['rename']);
    $book->execute();
    $book_id = $book->fetch();
    $author_name = explode(', ', $data['author']);
    $send_author = $pdo->prepare('INSERT INTO author (name_author) VALUES (:author)');
    $send_book = $pdo->prepare('INSERT INTO author_book VALUES (:author_id, :book_id)');
    $send_book->bindParam(':book_id', $book_id['book_id']);
    for ($i = 0, $len = count($author_name); $i < $len; $i++) {
        $send_author->bindValue(':author', $author_name[$i]); // Биндим значение цикла на :author.
        $send_author->execute();

        $author_id = $pdo->lastInsertId();
        $send_book->bindValue(':author_id', $author_id); // Передаем последний id добавленого автора в таблицу author_book.
        $send_book->execute();
    }
    echo '<br>' . "Редактирование прошло успешно" . '<br>';
} elseif (isset($data['add_lang'])) { // Добавление новых языков.
    $book = $pdo->prepare('SELECT
    DISTINCT  b.book_id
FROM author_book ab
         INNER JOIN book b ON b.book_id = ab.book_id
         INNER JOIN author a ON a.author_id = ab.author_id
WHERE b.title = :title');
    $book->bindParam(':title', $data['rename']);
    $book->execute();
    $book_id = $book->fetch();
    $lang = explode(', ', $data['language']);
    $send_lang = $pdo->prepare('INSERT INTO lang (lang) VALUES (:lang)');
    $send_book = $pdo->prepare('INSERT INTO book_lang VALUES (:book_id, :lang_id)');
    $send_book->bindParam(':book_id', $book_id['book_id']);
    for ($i = 0, $len = count($lang); $i < $len; $i++) {
        $send_lang->bindValue(':lang', $lang[$i]); // Биндим значение цикла на :lang.
        $send_lang->execute();

        $lang_id = $pdo->lastInsertId();
        $send_book->bindValue(':lang_id', $lang_id);
        $send_book->execute();
    }
    echo '<br>' . "Редактирование прошло успешно" . '<br>';
} elseif (isset($data['remove_author'])) { // Удаление автора.
    $query = $pdo->prepare('SELECT 
       a.author_id,
       b.title,
       a.name_author
        FROM author_book ab
          INNER JOIN book b ON b.book_id = ab.book_id
          INNER JOIN author a ON a.author_id = ab.author_id WHERE a.name_author = :name_author');
    $query->bindParam(':name_author', $data['unnecessary_author']);
    $query->execute();
    $key = $query->fetch();
    if ($data['rename'] == $key['title'] and $data['unnecessary_author'] == $key['name_author']) {
        $remove_author = $pdo->prepare('DELETE FROM `author_book` WHERE author_id = :author');
        $remove_author->bindParam(':author', $key['author_id']);
        $remove_author->execute();
        echo '<br>' . "Удаление прошло успешно" . '<br>';
    }
} elseif (isset($data['remove_language'])) {
    $query = $pdo->prepare('SELECT 
       l.lang_id,
       b.title,
       l.lang
       FROM book_lang bl
  INNER JOIN book b ON b.book_id = bl.book_id
  INNER JOIN lang l ON l.lang_id = bl.lang_id
   WHERE l.lang = :lang');
    $query->bindParam(':lang', $data['unnecessary_language']);
    $query->execute();
    $key = $query->fetch();
    if ($data['rename'] == $key['title'] and $data['unnecessary_language'] == $key['lang']) {
        $remove_author = $pdo->prepare('DELETE FROM `book_lang` WHERE lang_id = :lang_id');
        $remove_author->bindParam(':lang_id', $key['lang_id']);
        $remove_author->execute();
        echo '<br>' . "Удаление прошло успешно" . '<br>';
    }
} elseif (isset($data['remove_book'])) {
    $delete = $pdo->prepare('DELETE FROM `book` WHERE title = :title');
    $delete->bindParam(':title', $_POST['rename']);
    $delete->execute();
    echo '<br>' . "Удаление прошло успешно" . '<br>';
}