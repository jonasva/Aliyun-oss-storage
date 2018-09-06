<?php

namespace Jacobcyl\AliOSS\Listeners;

use Jacobcyl\AliOSS\Events\FileUpload;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class OssFileUpload implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FileUpload  $event
     * @return void
     */
    public function handle(FileUpload $event)
    {
        Storage::disk('oss')->putFileAs($event->file[0], new File(storage_path('app/public/').$event->file[1]), $event->file[2]);
        Storage::disk('local')->delete($event->file[1]);
    }
}
