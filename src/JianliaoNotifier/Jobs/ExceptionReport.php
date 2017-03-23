<?php

namespace JianliaoNotifier\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExceptionReport implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    protected $message;
    protected $code;
    protected $trace;

    public function __construct(Exception $e)
    {
        $this->message = $e->getMessage();
        $this->code = $e->getCode();
        $this->trace = $e->getTraceAsString();
    }

    public function handle()
    {
        $this->reportToJianLiao();
    }

    public function failed()
    {
        $this->delete();
    }

    private function reportToJianLiao(){
        $url = env('JIANLIAO_WEBHOOK');

        if(!$url) return;

        $post_data = [
            'title' => $this->message,
            'text' => $this->trace
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        curl_close($ch);
    }
}
