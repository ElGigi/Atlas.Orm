<?php
namespace Atlas\Orm\Table;

use Atlas\Orm\AtlasContainer;

class TableFactory
{
    protected $atlasContainer;
    protected $tableClass;

    public function __construct(AtlasContainer $atlasContainer, $tableClass)
    {
        $this->atlasContainer = $atlasContainer;
        $this->tableClass = $tableClass;
    }

    public function __invoke()
    {
        return new TableGateway(
            $this->atlasContainer->getConnectionLocator(),
            $this->atlasContainer->getQueryFactory(),
            $this->atlasContainer->getIdentityMap(),
            $this->atlasContainer->newInstance($this->tableClass),
            $this->atlasContainer->newInstance($this->tableClass . 'Events')
        );
    }
}
