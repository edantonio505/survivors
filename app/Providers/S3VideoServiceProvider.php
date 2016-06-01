<?php

namespace App\Providers;

use Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

class S3VideoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('s3_custom', function($app, $config) {
            $flysystemConfig = ['mimetype' => 'video/mp4'];

            $client = new S3Client([
                'credentials' => [
                    'key'    => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region' => $config['region'],
                'version' => '2006-03-01',
            ]);

            $adapter = new AwsS3Adapter($client, $config['bucket'], null, ['ContentType' => 'video/mp4']);
            $filesystem = new Filesystem($adapter);

            return $filesystem;
       });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
