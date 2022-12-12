<?php

namespace Controller;

use Exception;
use Service\BookService;

class FileProcessor
{
    private const EXTENSION_XML = 'xml';

    private BookService $bookService;

    public function __construct()
    {
        $this->bookService = new BookService();
    }

    /**
     * @throws Exception
     */
    public function processFiles(string $path): void
    {
        $this->validatePath($path);

        if (!is_dir($path)) {
            $this->processFile($path);
            return;
        }

        $this->processDirectory($path);
    }

    /**
     * @param string $path
     *
     * @return void
     *
     * @throws Exception
     */
    private function processDirectory(string $path): void
    {
        $files = array_filter(scandir($path), function ($fileName) {
            return $fileName !== '.' && $fileName !== '..';
        });

        if (empty($files)) {
            return;
        }

        foreach ($files as $fileName) {
            $currentPath = $path . '\\' . $fileName;

            if (is_dir($currentPath)) {
                $this->processDirectory($currentPath);
                continue;
            }

            $this->processFile($currentPath);
        }
    }

    /**
     * @param string $path
     *
     * @return void
     *
     * @throws Exception
     */
    private function validatePath(string $path): void
    {
        if (!file_exists($path)) {
            throw new Exception('Not an existing file or directory!');
        }
    }

    /**
     * @param string $path
     *
     * @return void
     *
     * @throws Exception
     */
    private function processFile(string $path): void
    {
        $fileNameParts = explode('.', $path);
        $extension = strtolower(end($fileNameParts));

        if ($extension !== self::EXTENSION_XML) {
            return;
        }

        $xmlFile = file_get_contents($path);
        $xmlString = simplexml_load_string($xmlFile);
        $jsonString = json_encode($xmlString);
        $contents = json_decode($jsonString, true);

        if (empty($contents['book'])) {
            return;
        }

        foreach ($contents['book'] as $book) {
            $author = $this->bookService->insertAuthor($book['author']);
            $this->bookService->insertBook($book['name'], $author->getId());

            echo 'Author: ' . $book['author'] . ' Title: ' . $book['name'] . "\n";
        }
    }
}