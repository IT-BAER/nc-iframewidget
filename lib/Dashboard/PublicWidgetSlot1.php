<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Dashboard;

/**
 * Public iFrame Widget Slot 1
 * 
 * This is the first (and default) public widget slot.
 * Maintains backward compatibility with existing installations.
 */
class PublicWidgetSlot1 extends PublicWidgetSlot
{
    protected function getSlotNumber(): int
    {
        return 1;
    }
}
