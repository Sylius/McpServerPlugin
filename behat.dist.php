<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use DMore\ChromeExtension\Behat\ServiceContainer\ChromeExtension;
use FriendsOfBehat\MinkDebugExtension\ServiceContainer\MinkDebugExtension;
use FriendsOfBehat\SuiteSettingsExtension\ServiceContainer\SuiteSettingsExtension;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use FriendsOfBehat\VariadicExtension\ServiceContainer\VariadicExtension;

return (new Config())
    ->import('tests/Behat/Resources/suites.php')
    ->withProfile(
        (new Profile('default'))
            ->withExtension(new Extension(ChromeExtension::class))
            ->withExtension(new Extension(MinkDebugExtension::class, [
                'directory' => 'etc/build',
                'clean_start' => false,
                'screenshot' => true,
            ]))
            ->withExtension(new Extension(MinkExtension::class, [
                'files_path' => '%paths.base%/vendor/sylius/sylius/src/Sylius/Behat/Resources/fixtures/',
                'base_url' => 'http://localhost:8080/',
                'default_session' => 'symfony',
                'javascript_session' => 'chrome',
                'sessions' => [
                    'symfony' => [
                        'symfony' => null,
                    ],
                    'chrome' => [
                        'chrome' => [
                            'api_url' => 'http://127.0.0.1:9222',
                            'validate_certificate' => false,
                        ],
                    ],
                ],
                'show_auto' => false,
            ]))
            ->withExtension(new Extension(SymfonyExtension::class, [
                'bootstrap' => 'vendor/sylius/test-application/config/bootstrap.php',
                'kernel' => [
                    'class' => 'Sylius\TestApplication\Kernel',
                ],
            ]))
            ->withExtension(new Extension(VariadicExtension::class))
            ->withExtension(new Extension(SuiteSettingsExtension::class, [
                'paths' => ['features'],
            ])),
    )
;
