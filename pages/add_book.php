<form action="add_book.php" method="post">
    <label>Введите название книги</label>
    <input type="text" placeholder="Enter book title" name="book_title">
    <br> <br>
    <label>Введите автора книги через запятую ", "</label>
    <input type="text" placeholder="Enter author" name="author"> <br><br>
    <label>Введите язык книги через запятую ", "</label>
    <input type="text" placeholder="Enter language" name="lang"><br><br>
    <hr>
    <button type="submit" name="submit">Отправить!</button>
</form>
<?php
require 'config.php';
$data = $_POST;
$sql = 'SELECT title, book_id FROM book WHERE title = :title';
$stmt = $pdo->prepare("$sql");
$stmt->bindParam(':title', $data['book_title']);
$stmt->execute();
$row = $stmt->fetch();
if (isset($data['submit'])) {
    if ($data['book_title'] === $row['title']) { // проверка на существование книги в базе.
        echo "Такая книга уже есть в базе, хотите ее '<a href='#'>'редактировать?";
    } elseif ($data['book_title'] == '') { // проверка на создание книги с пустым 'title'
        echo 'Зачем создавать книгу без названия? Ну думайте головой, немного';
    } elseif ($data['author'] == '') { // проверка на создание книги с автором
        echo 'Лучше ввести имя автора';
    } elseif ($data['lang'] == '') { // проверка на создание книги без языка
        echo 'Вы забыли указать язык, или просто проигнорировали просьбу.';
    } else {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            $send_title = $pdo->prepare('INSERT INTO book(title) VALUES(:title)'); // Передаем название книги в базу.
            $send_title->bindParam(':title', $data['book_title']);
            $send_title->execute();
            $book_id = $pdo->lastInsertId(); // Сохраняем значение последнего добавленого названия.

            $author_name = explode(', ', $data['author']); // Разделяем значения принятые в поле для ввода авторов, по запятой.
            $send_author = $pdo->prepare('INSERT INTO author (name_author) VALUES (:author)');

            $send_book = $pdo->prepare('INSERT INTO author_book VALUES (:author_id, :book_id)');
            $send_book->bindParam(':book_id', $book_id);

            for ($i = 0, $len = count($author_name); $i < $len; $i++) {
                $send_author->bindValue(':author', $author_name[$i]); // Биндим значение цикла на :author.
                $send_author->execute();

                $author_id = $pdo->lastInsertId();
                $send_book->bindValue(':author_id', $author_id); // Передаем последний id добавленого автора в таблицу author_book.
                $send_book->execute();
            }
            $language = explode(', ', $data['lang']);
            $send_language = $pdo->prepare('INSERT INTO lang (lang) VALUES (:lang)');
            $language_relations = $pdo->prepare('INSERT INTO book_lang VALUES (:book_id, :lang_id)');
            $language_relations->bindParam(':book_id', $book_id);
            for ($i = 0, $len = count($language); $i < $len; $i++) {
                $send_language->bindValue(':lang', $language[$i]);
                $send_language->execute();

                $language_id = $pdo->lastInsertId();
                $language_relations->bindValue(':lang_id', $language_id);
                $language_relations->execute();

            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }
    }
}
