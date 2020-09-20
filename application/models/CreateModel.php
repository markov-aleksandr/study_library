<?php

namespace Applicatiom\Models;

use Application\Core\Model;
use PDO;

class CreateModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function createBook($title, $author, $language, $isSubmit)
    {
        if (isset($isSubmit)) {
            if (!empty($title) && !empty($author) && !empty($language)) {
                try {
                    $this->dataConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->dataConnect->beginTransaction();
                    $this->addMultipleAttributesToBook($title, $author, $language);
                    $this->dataConnect->commit();
                } catch (Exception $e) {
                    $this->dataConnect->rollBack();
                }
            }
        }
    }

    private function addMultipleAttributesToBook($title, $author, $language)
    {
        $addTitle = $this->dataConnect->prepare('INSERT INTO book(title) VALUES (:title)');
        $addTitle->bindParam(':title', $title);
        $addTitle->execute();

        $bookId = $this->dataConnect->lastInsertId();

        $nameAuthor = explode(', ', $author);
        $addAuthor = $this->dataConnect->prepare('INSERT INTO author(name_author) VALUES (:author)');

        $innitBook = $this->dataConnect->prepare('INSERT INTO author_book VALUES (:authorId, :bookId)');
        $innitBook->bindParam(':bookId', $bookId);

        for ($i = 0, $len = count($nameAuthor); $i < $len; $i++) {
            $addAuthor->bindValue(':author', $nameAuthor[$i]); // Биндим значение цикла на :author.
            $addAuthor->execute();
            $authorId = $this->dataConnect->lastInsertId();

            $innitBook->bindValue(':authorId', $authorId); // Передаем последний id добавленого автора в таблицу author_book.
            $innitBook->execute();
        }

        $language = explode(', ', $language);
        $createLanguage = $this->dataConnect->prepare('INSERT INTO lang (lang) VALUES (:lang)');

        $addLanguage = $this->dataConnect->prepare('INSERT INTO book_lang VALUES (:bookId, :langId)');
        $addLanguage->bindParam(':bookId', $bookId);

        for ($i = 0, $len = count($language); $i < $len; $i++) {
            $createLanguage->bindValue(':lang', $language[$i]);
            $createLanguage->execute();

            $languageId = $this->dataConnect->lastInsertId();
            $addLanguage->bindValue(':langId', $languageId);
            $addLanguage->execute();

        }
    }

}