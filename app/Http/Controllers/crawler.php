<?php

namespace App\Http\Controllers;

use DOMDocument;
use Illuminate\Http\Request;

class crawler extends Controller
{
    $pageHashs = Array();
    $netResults = Array();
    public function crawl(Request $request)
    {
        $result=Array();
        $html =  file_get_contents($request->input('root'));
        if(!array_search(md5(html),$pageHashs))
        {
        array_push($pageHashs,md5($html));
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        foreach($links as $link)
        {
            array_push($result,$link->getAttribute('href'));
        }
        return json_encode($result);
    }
    }
}
