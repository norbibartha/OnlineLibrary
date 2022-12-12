CREATE DATABASE interpay;
CREATE TABLE authors (
                          id serial PRIMARY KEY,
                          name character varying(100) UNIQUE NOT NULL
);

CREATE TABLE books (
                         id serial PRIMARY KEY,
                         title character varying(100) NOT NULL,
                         author_id INTEGER REFERENCES authors (id)
);

ALTER TABLE books
    ADD CONSTRAINT uq_books UNIQUE(title, author_id);

CREATE INDEX idx_authors_name
    ON authors(name);