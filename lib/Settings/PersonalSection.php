<?php

declare(strict_types=1);

namespace OCA\IframeWidget\Settings;

use OCA\IframeWidget\AppInfo\Application;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class PersonalSection implements IIconSection {
    private IL10N $l;
    private IURLGenerator $urlGenerator;

    public function __construct(IL10N $l, IURLGenerator $urlGenerator) {
        $this->l = $l;
        $this->urlGenerator = $urlGenerator;
    }    public function getID(): string {
        return Application::APP_ID;
    }

    public function getName(): string {
        return $this->l->t('iFrame Widget');
    }

    public function getPriority(): int {
        return 55;
    }

    public function getIcon(): string {
        return $this->urlGenerator->imagePath(Application::APP_ID, 'iframewidget-dark.svg');
    }
}
