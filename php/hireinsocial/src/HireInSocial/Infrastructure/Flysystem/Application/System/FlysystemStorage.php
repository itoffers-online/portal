<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HireInSocial\Infrastructure\Flysystem\Application\System;

use HireInSocial\Application\Assertion;
use HireInSocial\Application\System\FileStorage;
use HireInSocial\Application\System\FileStorage\File;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Azure\AzureAdapter;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Common\ServicesBuilder;

final class FlysystemStorage implements FileStorage
{
    private $filesystem;
    private $config;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public static function create(array $config) : self
    {
        Assertion::allInArray(['type'], \array_keys($config));
        Assertion::inArray($config['type'], ['local', 'azure'], 'Missing or invalid filesystem type');

        switch ($config['type']) {
            case 'local':
                Assertion::allInArray(
                    [
                        'local_storage_path',
                        'storage_url',
                    ],
                    \array_keys($config)
                );

                $storage = new self(
                    new Filesystem(
                        new Local(
                            $config['local_storage_path']
                        )
                    )
                );

                break;
            case 'azure':
                Assertion::allInArray(
                    [
                        'azure_storage_account_name',
                        'azure_storage_account_key',
                        'azure_storage_container',
                    ],
                    \array_keys($config)
                );

                $storage = new self(
                    new Filesystem(
                        new AzureAdapter(
                            ServicesBuilder::getInstance()->createBlobService(
                                \sprintf(
                                    'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s',
                                    $config['azure_storage_account_name'],
                                    $config['azure_storage_account_key']
                                )
                            ),
                            $config['azure_storage_container']
                        )
                    )
                );

                break;
        }

        $storage->config = $config;

        return $storage;
    }

    public function config()
    {
        return $this->config;
    }

    public function upload(File $file): void
    {
        $this->filesystem->put($file->destinationPath(), \file_get_contents($file->tmpPath()));
    }

    public function purge(): void
    {
        \array_map(
            function (array $file) {
                if ($file['type'] === 'file') {
                    $this->filesystem->delete($file['path']);
                } else {
                    $this->filesystem->deleteDir($file['path']);
                }
            },
            $this->filesystem->listContents()
        );
    }
}
