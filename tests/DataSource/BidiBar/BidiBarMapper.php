<?php
namespace Atlas\Orm\DataSource\BidiBar;

use Atlas\Orm\Mapper\AbstractMapper;
use Atlas\Orm\DataSource\BidiFoo\BidiFooMapper;

/**
 * @inheritdoc
 */
class BidiBarMapper extends AbstractMapper
{
    /**
     * @inheritdoc
     */
    protected function setRelated()
    {
        $this->oneToOneBidi('foo', BidiFooMapper::CLASS)
            ->on(['bidifoo_id' => 'bidifoo_id']);
    }
}
