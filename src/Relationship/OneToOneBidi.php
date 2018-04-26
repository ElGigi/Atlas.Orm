<?php
/**
 *
 * This file is part of Atlas for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Atlas\Orm\Relationship;

use Atlas\Orm\Mapper\RecordInterface;
use SplObjectStorage;

/**
 *
 * Defines a bidirectional one-to-one relationship.
 *
 * @package atlas/orm
 *
 */
class OneToOneBidi extends OneToOne
{
    public function fixNativeRecordKeys(RecordInterface $nativeRecord) : void
    {
        $foreignRecord = $nativeRecord->{$this->name};
        if (! $foreignRecord instanceof RecordInterface) {
            return;
        }

        $this->initialize();

        foreach ($this->getOn() as $nativeField => $foreignField) {
             $nativeRecord->$nativeField = $foreignRecord->$foreignField;
        }
    }

    /**
     *
     * Given a native Record, persists the related foreign Records.
     *
     * @param RecordInterface $nativeRecord The native Record being persisted.
     *
     * @param SplObjectStorage $tracker Tracks which Record objects have been
     * operated on, to prevent infinite recursion.
     *
     */
    public function persistForeign(RecordInterface $nativeRecord, SplObjectStorage $tracker) : void
    {
        $this->persistForeignRecord($nativeRecord, $tracker);
        $this->fixNativeRecordKeys($nativeRecord);
        $this->nativeMapper->update($nativeRecord);
    }
}
