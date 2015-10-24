<?php
namespace Atlas\DataSource\Reply;

use Atlas\DataSource\Author\AuthorMapper;
use Atlas\Mapper\AbstractRelations;

class ReplyRelations extends AbstractRelations
{
    protected function setRelations()
    {
        $this->belongsTo('author', AuthorMapper::CLASS);
    }
}
