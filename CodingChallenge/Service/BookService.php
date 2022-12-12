<?php

namespace Service;

use Database\DatabaseClient;
use Database\DatabaseClientFactory;
use Entity\Author;
use Entity\Book;
use Exception;

class BookService
{
    private DatabaseClient $client;

    public function __construct()
    {
        $this->client = DatabaseClientFactory::getClient();
    }

    /**
     * @param string $authorName
     *
     * @return array
     *
     * @throws Exception
     */
    public function getBooksByAuthor(string $authorName): array
    {
        $books = $this->client->searchBooksByAuthor(['name' => $authorName]);

        return array_map(function ($item) {
            return new Book(...$item);
        }, $books);
    }

    /**
     * @param string $bookName
     * @param int $authorId
     *
     * @return void
     *
     * @throws Exception
     */
    public function insertBook(string $bookName, int $authorId): void
    {
        $book = $this->client->getBook(['title' => $bookName, 'author_id' => $authorId]);
        if (empty($book)) {
            $this->client->insert(DatabaseClient::TABLE_NAME_BOOKS, ['title', 'author_id'], [$bookName, $authorId]);
        }
    }

    /**
     * @param string $authorName
     *
     * @return Author
     *
     * @throws Exception
     */
    public function insertAuthor(string $authorName): Author
    {
        $authorData = $this->client->getAuthor(['name' => $authorName]);

        if (empty($authorData)) {
            $this->client->insert(DatabaseClient::TABLE_NAME_AUTHORS, ['name'], [$authorName]);
            $authorData = $this->client->getAuthor(['name' => $authorName]);
        }

        return new Author(...$authorData);
    }
}