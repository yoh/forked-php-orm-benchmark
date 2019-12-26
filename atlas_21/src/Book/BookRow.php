<?php
/**
 * This file was generated by Atlas. Changes will be overwritten.
 */
declare(strict_types=1);

namespace Book;

use Atlas\Table\Row;

/**
 * @property mixed $id INTEGER NOT NULL
 * @property mixed $title VARCHAR(255) NOT NULL
 * @property mixed $isbn VARCHAR(24) NOT NULL
 * @property mixed $price FLOAT
 * @property mixed $author_id INTEGER
 */
class BookRow extends Row
{
    protected $cols = [
        'id' => null,
        'title' => null,
        'isbn' => null,
        'price' => null,
        'author_id' => null,
    ];
}
