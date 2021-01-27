<?php

namespace App\Http\Controllers;

use DOMDocument;
use Illuminate\Http\Request;

class crawler extends Controller
{
    $this->pageHashs = array();
    $this->netResults = array();
    public function crawl(Request $request)
    {
        $result=Array();
        $html =  file_get_contents($request->input('root'));
        array_push($netResults,$html);
        if(!array_search(md5(html),$pageHashs))
        {
        array_push($pageHashs,md5($html));
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        foreach($links as $link)
        {
            echo $link->getAttribute('href');
            ob_flush();
            return crawl($link->getAttribute('href'));

        }
    }
    else
    {
        return "Completed";
    }
    }
}
