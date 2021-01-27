<?php

namespace App\Http\Controllers;

use DOMDocument;
use Illuminate\Http\Request;

class crawler extends Controller
{
    public $pageHashs = array();
    public $netResults = array();
    public $depth = 0;
    public $initialRoot;
    public function crawl(Request $request)
    {
        $this->initialRoot = $request->input('root');
        $this->startCrawl($request->input('root'));
        return json_encode($this->netResults);
    }
public function startCrawl($root)
{
    error_log('Crawling : '.$root);
    $this->depth++;
    $rootHash = md5($root);

    if($this->exists($rootHash,$this->pageHashs))
    {
        error_log('Duplicate. reject');
        return;
    }
    error_log('Processing, '.$root);
    try{
    $html =  file_get_contents($root);
    }
    catch(Exception $e){
        error_log($e->getMessage());
    }
    array_push($this->netResults,$root);
    array_push($this->pageHashs,$rootHash);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $links = $dom->getElementsByTagName('a');
    foreach($links as $link)
    {
        $url = $link->getAttribute('href');
        $this->startCrawl($this->urlInspector($url));
    }

}
public function urlInspector($url){
    if(substr($url,0,4)=='http')
{
    error_log(substr($url,4,1));
    if(substr($url,4,1)=='s')
    {
        return $this->initialRoot;
    }
    return $url;
}
else
{
    if($url=='/'){
    return ($this->initialRoot);}
    else{
    return ($this->initialRoot.$url);}
}
}
public function exists($hash){
$exist=false;
    for($i=0;$i<count($this->pageHashs);$i++)
    {
        if($hash==$this->pageHashs[$i])
        {
            $exist=true;
            return $exist;
        }
    }
    return $exist;
}
}

