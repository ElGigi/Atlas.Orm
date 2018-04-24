<?php
namespace Atlas\Orm\DataSource\BidiFoo;

use Atlas\Orm\Mapper\AbstractMapper;
use Atlas\Orm\DataSource\BidiBar\BidiBarMapper;

/**
 * @inheritdoc
 */
class BidiFooMapper extends AbstractMapper
{
    /**
     * @inheritdoc
     */
    protected function setRelated()
    {
        $this->oneToOneBidi('bar', BidiBarMapper::CLASS)
            ->on(['bidibar_id' => 'bidibar_id']);
    }
}
