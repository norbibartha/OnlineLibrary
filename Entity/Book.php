<?php

namespace Entity;

class Book
{
    private int $id;
    private string $title;
    private int $authorId;

    public function __construct(int $id, string $title, int $authorId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->authorId = $authorId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     */
    public function setAuthorId(int $authorId): void
    {
        $this->authorId = $authorId;
    }

    public function normalize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'authorId' => $this->getAuthorId(),
        ];
    }
}