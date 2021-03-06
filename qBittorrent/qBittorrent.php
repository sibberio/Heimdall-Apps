<?php namespace App\SupportedApps\qBittorrent;

class qBittorrent extends \App\SupportedApps implements \App\EnhancedApps {

    public $config;

    //protected $login_first = true; // Uncomment if api requests need to be authed first
    //protected $method = 'POST';  // Uncomment if requests to the API should be set by POST

    function __construct() {
        //$this->jar = new \GuzzleHttp\Cookie\CookieJar; // Uncomment if cookies need to be set
    }

    public function test()
    {
        $test = parent::appTest($this->url('version/api'));
        echo $test->status;
    }

    public function livestats()
    {
        $status = 'inactive';
        $res = parent::execute($this->url('query/torrents'));
        $details = json_decode($res->getBody());

        $data = [];


        $torrents = $details;
        $torrentCount = count($torrents);
        $rateDownload = $rateUpload = $completedTorrents = 0;
        foreach ($torrents as $thisTorrent) {
            $rateDownload += $thisTorrent->dlspeed;
            $rateUpload += $thisTorrent->upspeed;
            if ($thisTorrent->progress == 1) {
                $completedTorrents += 1;
            }
        }
        $leech = $torrentCount - $completedTorrents;
        if ($leech > 0) {
            $status = 'active';
        }

        $data['download_rate'] = format_bytes($rateDownload, false, ' <span>', '/s</span>');
        $data['upload_rate'] = format_bytes($rateUpload, false, ' <span>', '/s</span>');
        $data['seed_count'] = $completedTorrents ?? 0;
        $data['leech_count'] = $leech ?? 0;  




        return parent::getLiveStats($status, $data);
        
    }
    public function url($endpoint)
    {
        $api_url = parent::normaliseurl($this->config->url).$endpoint;
        return $api_url;
    }
}
