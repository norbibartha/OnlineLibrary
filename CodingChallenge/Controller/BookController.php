<?php

namespace Controller;

use Entity\Book;
use Exception;
use Service\BookService;

class BookController
{
    private BookService $bookService;

    public function __construct()
    {
        $this->bookService = new BookService();
    }

    /**
     * @param array $params
     *
     * @return string
     *
     * @throws Exception
     */
    public function getBooks(array $params): string
    {
        if (empty($params['author'])) {
            return $this->constructResponse(null, 400, 'Bad request');
        }

        $books = $this->bookService->getBooksByAuthor($params['author']);
        if (empty($books)) {
            return $this->constructResponse(null, 200, 'No records found');
        }

        return $this->constructResponse($books, 200, 'Success');
    }

    /**
     * @param array|null $books
     * @param int $responseCode
     * @param string $responseMessage
     *
     * @return string
     */
    private function constructResponse(?array $books, int $responseCode, string $responseMessage): string
    {
        if (!empty($books)) {
            /** @var Book $book */
            foreach ($books as $book) {
                $response['books'][] = $book->normalize();
            }
        }

        $response['responseCode'] = $responseCode;
        $response['responseMessage'] = $responseMessage;

        return json_encode($response);
    }
}