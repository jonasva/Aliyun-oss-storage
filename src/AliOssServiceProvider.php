<?php

namespace Jacobcyl\AliOSS;

use Jacobcyl\AliOSS\Plugins\PutFile;
use Jacobcyl\AliOSS\Plugins\PutRemoteFile;
use Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use OSS\OssClient;
use Illuminate\Support\Facades\Event;

class AliOssServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('Jacobcyl\AliOSS\Events\FileUpload', 'Jacobcyl\AliOSS\Listeners\OssFileUpload');
        //发布配置文件
        /*
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/config/config.php' => config_path('alioss.php'),
            ], 'config');
        }
        */

        Storage::extend('oss', function($app, $config)
        {
            $accessId       = $config['access_id'];
            $accessKey      = $config['access_key'];
            $endPoint       = $config['endpoint']; // 默认作为外部节点
            $epInternal     = empty($config['endpoint_internal']) ? $endPoint : $config['endpoint_internal']; // 内部节点
            $cdnDomain      = empty($config['cdnDomain']) ? '' : $config['cdnDomain'];
            $bucket         = $config['bucket'];
            $ssl            = empty($config['ssl']) ? false : $config['ssl'];
            $isCname        = empty($config['isCName']) ? false : $config['isCName'];
            $debug          = empty($config['debug']) ? false : $config['debug'];
            $maxFileSize    = empty($config['maxFileSize']) ? 6000000 : $config['maxFileSize'];

            if($debug) Log::debug('OSS config:', $config);

            $client  = new OssClient($accessId, $accessKey, $epInternal, $isCname);
            $adapter = new AliOssAdapter($client, $bucket, $endPoint, $ssl, $isCname, $debug, $cdnDomain, $maxFileSize);
            
            $filesystem =  new Filesystem($adapter);
            
            $filesystem->addPlugin(new PutFile());
            $filesystem->addPlugin(new PutRemoteFile());
            //$filesystem->addPlugin(new CallBack());
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
    }

}
