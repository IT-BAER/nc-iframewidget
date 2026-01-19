<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Dashboard;

/**
 * Group iFrame Widget Slot 1
 * 
 * This is the first (and default) group widget slot.
 * Maintains backward compatibility with existing installations.
 */
class GroupWidgetSlot1 extends GroupWidgetSlot
{
    protected function getSlotNumber(): int
    {
        return 1;
    }
}
