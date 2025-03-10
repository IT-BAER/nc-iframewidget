<?php

namespace OCA\IframeWidget\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

use OCA\IframeWidget\AppInfo\Application;

class Section implements IIconSection
{
    /** @var IL10N */
    private $l;

    /** @var IURLGenerator */
    private $url;

    /**
     * Section constructor.
     *
     * @param IL10N $l
     * @param IURLGenerator $url
     */
    public function __construct(IL10N $l, IURLGenerator $url)
    {
        $this->l = $l;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return Application::APP_ID;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->l->t('iFrame Widget');
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 80;
    }

    /**
     * @return string The icon URL
     */
    public function getIcon(): string
    {
        return $this->url->imagePath(Application::APP_ID, 'iframewidget-dark.svg');
    }
}
