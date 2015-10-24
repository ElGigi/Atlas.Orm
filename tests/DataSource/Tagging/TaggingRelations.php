<?php
namespace Atlas\DataSource\Tagging;

use Atlas\Mapper\AbstractRelations;

class TaggingRelations extends AbstractRelations
{
    protected function getNativeMapperClass()
    {
        return TaggingMapper::CLASS;
    }

    protected function setRelations()
    {
        // no relations
    }
}
