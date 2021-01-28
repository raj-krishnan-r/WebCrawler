<?php

namespace App\Http\Controllers;

use DOMDocument;
use Illuminate\Http\Request;
use App\SearchEntry;
use finfo;

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
        return "Completed";
    }
public function startCrawl($root)
{
     
    $this->depth++;
    $rootHash = md5($root);
    if($this->exists($rootHash,$this->pageHashs))
    {
        error_log('Duplicate. reject');
        return;
    }
    error_log('Processing, '.$root);
    @$html = file_get_contents($root);
//Mime
    $file_info = new finfo(FILEINFO_MIME_TYPE);
    $mime_type=$file_info->buffer($html);
    error_log($mime_type);
    //if($mime_type=="")
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    //Feedback

    $titles = $dom->getElementsByTagName('title');
    $title='';
    foreach($titles as $title)
    {
        $title= $title->textContent;
    }
   //Insertion to dB
   if($this->persistantStorage($title,$html,$root)){
       error_log('Indexed');
   }
   else
   {
       error_log('Not Indexed');
   }
array_push($this->pageHashs,$rootHash);
echo $root." @ ".$title."</br>";
ob_flush();
    //
    $links = $dom->getElementsByTagName('a');
    foreach($links as $link)
    {
        $url = $link->getAttribute('href');
        if(!$this->hasProtocol($url)){
        $this->startCrawl($this->urlInspector($url));
        }
    }

}
public function urlInspector($url){
if($this->hasProtocol($url))
{
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
public function hasProtocol($url){
    $protocol=false;
    if(substr($url,0,7)=='http://'){
        $protocol=true;
    }
    else if(substr($url,0,8)=='https://')
    {
        $protocol=true;
    }
    else if(substr($url,0,4)=='tel:')
    {
        $protocol=true;
    }
    else if(substr($url,0,7)=='mailto:')
    {
        $protocol=true;
    }
    else{
        $protocol=false;
    }
    return $protocol;
    
}
public function persistantStorage($title,$content,$root){
$entry = SearchEntry::updateOrCreate(
    ['rootHash'=>md5($root)],
    ['title'=>$title,'content'=>addslashes(preg_replace('/\s+/', ' ', strip_tags(($content)))),'content_hash'=>md5($content),'root'=>$root]
);
if($entry)
{
    return true;
}
else{
    return false;
}
}

}

