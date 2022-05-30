<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use App\Models\Feed;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $urls = Feed::all();
        if(!empty($urls)){
          foreach($urls as $url){
              $context = stream_context_create(array('ssl'=>array(
              'verify_peer' => false, 
              "verify_peer_name"=>false
              )));
              libxml_set_streams_context($context);
              libxml_use_internal_errors(true);
              try {
                $rss_feed = simplexml_load_file($url->url);
                if(!empty($rss_feed->channel->item)){
                  foreach($rss_feed->channel->item as $feed_item) {
                    $response = [];
                    $categories = [];
                    $timestamp = Carbon::parse($feed_item->pubDate);
                    $newDate = $timestamp->format('Y-m-d');
                    $link = str_replace(">","",$feed_item->link);
                    if($this->isUrlExist($link)){
                      continue;
                    }
                    if(!empty($feed_item->category)){
                      foreach($feed_item->category as $category){
                        $categories[]  = (string) $category;
                      }
                    }
                    $response  = $this->getSingleUrlData($link,$newDate);
                    $response['site_id'] = $url->site_id;
                    $response['publish_date'] = $newDate;
                    $response['category'] = (!empty($categories))?implode(",",$categories):implode(",",$response['category']);
                    //echo '<pre>';print_r($response); //break;
                    Post::create($response)->id;
                  }
                }
              }
              catch (Exception $e) {
                Log::useDailyFiles(storage_path().'/logs/newsfeeds.log');
                Log::info('Exception Captured: ',  $e->getMessage(), "\n");
              } 
          }
      }
    }

    public function isUrlExist($live_link){
        $post = Post::where('live_link', '=', $live_link)->get();
        return $post->count();die;
    }

    public function getAttributeValue($attributes,$attribute_name){
      $value = '';
      if(!empty($attributes)){
        foreach($attributes as $attribute){
          if($attribute->name == $attribute_name){
            $value = $attribute->value;
          }
        }
      }
      return $value;
    }

    public function getSingleUrlData($url,$date){
        $response = [];
        if(strpos($url,"dev.albilad.site")){
            $response = $this->getAlbiladContent($url,$date);
          }
          else if(strpos($url,"arabnews.com")){
            $response = $this->getArabNewsContent($url,$date);
          }
          else if(strpos($url,"saudigazette.com.sa")){
            $response = $this->getSaudigazetteContent($url,$date);
          }
          else if(strpos($url,"okaz.com.sa")){
            $response = $this->getOkazContent($url,$date);
          }
          else if(strpos($url,"aleqt.com")){
            $response = $this->getAleqtContent($url,$date);  
          }
          else if(strpos($url,"al-jazirah.com")){
            $response = $this->getJazirahContent($url,$date);  
          }
          else if(strpos($url,"aawsat.com")){
            $response = $this->getAawsatContent($url,$date); 
          }
          else if(strpos($url,"alriyadh.com")){
            $response = $this->getAlriyadhContent($url,$date);
          }
          else if(strpos($url,"alwatan.com.sa")){
            $response = $this->getAlwatanContent($url,$date);
          }
          else if(strpos($url,"al-madina.com")){
            $response = $this->getMadinaContent($url,$date);
          }
          else if(strpos($url,"hasatoday.com")){
            $response = $this->getHasatodayContent($url,$date);
          }
          else if(strpos($url,"alweeam.com.sa")){
            $response = $this->getAlweeamContent($url,$date);
          }
          else if(strpos($url,"almowaten.net")){
            $response = $this->getAlmowatenContent($url,$date);
          }
          else if(strpos($url,"makkahnewspaper.com")){
            $response = $this->getMakkahNewsPaperContent($url,$date);
          }
          else if(strpos($url,"alyaum.com")){
            $response = $this->getAlyaumContent($url,$date);
          }
          else if(strpos($url,"spa.gov.sa")){
            $response = $this->getSpaContent($url,$date);
          }
          return $response;
    }

    public function getDomContent($link){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function getAlbiladContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($data);
        $xpath = new \DOMXPath($dom);

        $query = '//*/div[starts-with(@class, \'cs-entry__category\')]//div[starts-with(@class, \'cs-meta-category\')]//ul';
        $cats = $xpath->query($query);
        if(!empty($cats)){
          $li_tags = $cats[0]->getElementsByTagName('li');
          if(!empty($li_tags)){
              foreach($li_tags as $key=>$li_tag){
                $a_tag = $li_tag->getElementsByTagName('a');
                if(!in_array($a_tag->item(0)->nodeValue,$cat_array)){
                  $cat_array[] = $a_tag->item(0)->nodeValue;
                }
              }
          }
        }

        $metas = $dom->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
                $title = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $image_src = $meta->getAttribute('content');
            }
        }
        $main_description = '';
        $query = '//*/div[starts-with(@class, \'entry-content\')]';
        $contents = $xpath->query($query);
        foreach ($contents as $content) {
          $main_description .= $dom->saveHTML($content);
        }

        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getArabNewsContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($data);
        $xpath = new \DOMXPath($dom);

        $query = '//*/div[starts-with(@class, \'entry-tags\')]';
        $cats = $xpath->query($query);
        if(!empty($cats)){
            $tags = $cats->item(0)->getElementsByTagName('a');
            foreach ($tags as $key=>$tag) {
                if(!in_array($tag->nodeValue,$cat_array)){
                $cat_array[] = $tag->nodeValue;
                }
            } 
        }

        $metas = $dom->getElementsByTagName('meta');

        for($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if($meta->getAttribute('property') == 'og:title') {
              $title= $meta->getAttribute('content');
            }
            if($meta->getAttribute('property') == 'og:image') {
              $image_src = $meta->getAttribute('content');
            }
        }

        $main_description = '';
        $query = '//*/div[starts-with(@class, \'entry-content\')]//div[contains(@class, \'even\')]';
        $contents = $xpath->query($query);
        foreach ($contents as $content) {
          $main_description .= $dom->saveHTML($content);
        }

        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getSaudigazetteContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($data);
        $xpath = new \DOMXPath($dom);

        $metas = $dom->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
                $title = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $image_src = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'article:section') {
               $cat_array[] = $meta->getAttribute('content');
            }
        }
        $main_description = '';
        $query = '//*/div[starts-with(@class, \'article-body\')]';
        $contents = $xpath->query($query);
        foreach ($contents as $content) {
          $main_description .= $dom->saveHTML($content);
        }
        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getOkazContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($data);
        $xpath = new \DOMXPath($dom);

        $metas = $dom->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
                $title = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $image_src = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'article:section') {
               $cat_array[] = $meta->getAttribute('content');
            }
        }
        $main_description = '';
          $query = '//*/div[starts-with(@class, \'bodyText\')]';
          $contents = $xpath->query($query);
          foreach ($contents as $content) {
            $main_description .= $dom->saveHTML($content);
          }

        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getAleqtContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $query = '//*/span[starts-with(@class, \'title-inner\')]//span';
        $cats = $xpath->query($query);
        if(!empty($cats)){
          $tags = $cats->item(0)->getElementsByTagName('a');
          $cat_array[] = $cats->item(0)->nodeValue;
        }

        $metas = $dom->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
              $title= $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $image_src = $meta->getAttribute('content');
            }
        }
        $main_description = '';
        $query = '//*/div[starts-with(@class, \'entry-content\')]';
        $contents = $xpath->query($query);
        foreach ($contents as $content) {
          $main_description .= $dom->saveHTML($content);
        }

        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getJazirahContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $query = '//*/input[starts-with(@name, \'print_subject\')]';
        $cats = $xpath->query($query);
        if(!empty($cats)){
            $cat_array[] = $cats->item(0)->getAttribute('value');
        }

        $metas = $dom->getElementsByTagName('meta');
        $body = "";
        for ($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
                $title = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $image_src = $meta->getAttribute('content');
            }
        }
        $main_description = '';
        $query = '//*/div[starts-with(@class, \'writers-blk-bttm-left\')]//p';
        $contents = $xpath->query($query);
        foreach ($contents as $content) {
          $main_description .= $dom->saveHTML($content);
        }

        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getAawsatContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $query = '//*/ol[starts-with(@class, \'breadcrumb\')]//li[starts-with(@class, \'first\')]';
        $cats = $xpath->query($query);
        if(!empty($cats)){
            $cat_array[] = $cats->item(0)->nodeValue;
        }
        $metas = $dom->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
               $title= $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $image_src = $meta->getAttribute('content');
            }
        }
        $main_description = '';
        $query = '//*/div[starts-with(@class, \'node_new_body\')]';
        $contents = $xpath->query($query);
        foreach ($contents as $content) {
          $main_description .= $dom->saveHTML($content);
        }

        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getAlriyadhContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $cat_array = [];
        $query = '//*/ol[starts-with(@class, \'breadcrumb\')]//li[starts-with(@class, \'active\')]';
        $cats = $xpath->query($query);
        if(!empty($cats)){
          $cat_array[] = $cats->item(0)->nodeValue;
        }
        $metas = $dom->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
              $title= $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $image_src = $meta->getAttribute('content');
            }
        }
        $main_description = '';
        $query = '//*/div[contains(@class, \'article-text\')]';
        $content = $xpath->query($query);
        foreach ($content->item(0)->getElementsByTagName('p') as $tag) {
          $main_description .= '<p class="card-text">'.$tag->nodeValue.'</p>';
        }

        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getAlwatanContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $metas = $dom->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
              $title= $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $image_src = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'article:section') {
               $cat_array[] = $meta->getAttribute('content');
            }
        }
        $main_description = '';
        $query = '//*/div[starts-with(@class, \'articleBody\')]';
        $contents = $xpath->query($query);
        foreach ($contents as $content) {
          $main_description .= $dom->saveHTML($content);
        }
        
        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getMadinaContent($item_url,$date){
        $input = $cat_array = [];
        $image_src = '';
        $data = $this->getDomContent($item_url);
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $metas = $dom->getElementsByTagName('meta');
        $body = "";
        for ($i = 0; $i < $metas->length; $i ++) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') == 'og:title') {
              $title= $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $image_src = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'article:section') {
               $cat_array[] = $meta->getAttribute('content');
            }
        }
        $main_description = '';
        $query = '//*/div[starts-with(@class, \'article-body\')]';
        $contents = $xpath->query($query);
        foreach ($contents as $content) {
          $main_description .= $dom->saveHTML($content);
        }

        $input['title'] = $title;
        $input['image'] = $image_src;
        
        $input['live_link'] = $item_url;
        $input['category'] = $cat_array;
        $input['main_description'] = $main_description;
        return $input;
    }

    public function getHasatodayContent($item_url,$date){
      $input = $cat_array = [];
      $title = $main_description = $image_src = '';
      $data = $this->getDomContent($item_url);
      $dom = new \DOMDocument();
      @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
      $xpath = new \DOMXPath($dom);
      $query = '//*/div[starts-with(@class, \'post-header\')]//h1';
      $title_contents = $xpath->query($query);
      if(!empty($title_contents)){
        $title.= $title_contents->item(0)->nodeValue;
      }
      $query = '//*/div[starts-with(@class, \'post-header\')]//div[starts-with(@class, \'single-featured\')]//a//img';
      $image_contents = $xpath->query($query);
      if(!empty($image_contents)){
        $attributes = $image_contents->item(0)->attributes;
        $image_src .= $this->getAttributeValue($attributes,'data-src');
      }
      $query = '//*/div[starts-with(@class, \'pf-content\')]';
      $contents = $xpath->query($query);
      foreach ($contents as $content) {
        $main_description .= $dom->saveHTML($content);
      }

      $input['title'] = $title;
      $input['image'] = $image_src;
      $input['live_link'] = $item_url;
      $input['main_description'] = $main_description;
      return $input;
  }

  public function getAlweeamContent($item_url,$date){
    $input = $cat_array = [];
    $title = $main_description = $image_src = '';
    $data = $this->getDomContent($item_url);
    $dom = new \DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new \DOMXPath($dom);
    $metas = $dom->getElementsByTagName('meta');
    for ($i = 0; $i < $metas->length; $i ++) {
      $meta = $metas->item($i);
      if ($meta->getAttribute('property') == 'og:title') {
        $title .= $meta->getAttribute('content');
      }
      if ($meta->getAttribute('property') == 'og:image') {
          $image_src .= $meta->getAttribute('content');
      }
    }
    $query = '//*/div[starts-with(@class, \'entry-content\')]//p';
    $contents = $xpath->query($query);
    foreach ($contents as $content) {
      $main_description .= $dom->saveHTML($content);
    }

    $input['title'] = $title;
    $input['image'] = $image_src;
    $input['live_link'] = $item_url;
    $input['main_description'] = $main_description;
    return $input;
 }

 public function getAlmowatenContent($item_url,$date){
    $input = $cat_array = [];
    $title = $main_description = $image_src = '';
    $data = $this->getDomContent($item_url);
    $dom = new \DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new \DOMXPath($dom);
    $query = '//*/div[starts-with(@class, \'entry-header\')]//h1';
    $title_contents = $xpath->query($query);
    if(!empty($title_contents)){
      $title.= $title_contents->item(0)->nodeValue;
    }
    $query = '//*/div[contains(@class, \'featured_image\')]//a//div//img';
    $image_contents = $xpath->query($query);
    if(!empty($image_contents)){
      $attributes = $image_contents->item(0)->attributes;
      $image_src .= $this->getAttributeValue($attributes,'data-lazy-src');
    }
    $query = '//*/div[starts-with(@class, \'entry-content\')]//p';
    $contents = $xpath->query($query);
    foreach ($contents as $content) {
      $main_description .= $dom->saveHTML($content);
    }

    $input['title'] = $title;
    $input['image'] = $image_src;
    $input['live_link'] = $item_url;
    $input['main_description'] = $main_description;
    return $input;
  }

  public function getMakkahNewsPaperContent($item_url,$date){
    $input = $cat_array = [];
    $title = $main_description = $image_src = '';
    $data = $this->getDomContent($item_url);
    $dom = new \DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new \DOMXPath($dom);

    $metas = $dom->getElementsByTagName('meta');
    for ($i = 0; $i < $metas->length; $i ++) {
        $meta = $metas->item($i);
        if ($meta->getAttribute('property') == 'og:image') {
          $image_src = $meta->getAttribute('content');
        }
        if ($meta->getAttribute('property') == 'article:section') {
            $cat_array[] = $meta->getAttribute('content');
        }
    }

    $query = '//*/div[starts-with(@class, \'holder-article__title\')]//h1';
    $title_contents = $xpath->query($query);
    if(!empty($title_contents)){
      $title.= $title_contents->item(0)->nodeValue;
    }

    $query = '//*/div[starts-with(@class, \'section-main-article\')]//div[starts-with(@class, \'article-desc\')]';
    $contents = $xpath->query($query);
    foreach ($contents as $content) {
      $main_description .= $dom->saveHTML($content);
    }
    
    $input['title'] = $title;
    $input['image'] = $image_src;
    $input['live_link'] = $item_url;
    $input['category'] = $cat_array;
    $input['main_description'] = $main_description;
    return $input;
  }

  public function getAlyaumContent($item_url,$date){
    $input = $cat_array = [];
    $title = $main_description = $image_src = '';
    $data = $this->getDomContent($item_url);
    $dom = new \DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new \DOMXPath($dom);

    $metas = $dom->getElementsByTagName('meta');
    for ($i = 0; $i < $metas->length; $i ++) {
        $meta = $metas->item($i);
        if ($meta->getAttribute('property') == 'og:image') {
          $image_src = $meta->getAttribute('content');
        }
        if ($meta->getAttribute('property') == 'article:section') {
            $cat_array[] = $meta->getAttribute('content');
        }
    }

    $query = '//*/div[starts-with(@class, \'aksa-to1\')]//h1';
    $title_contents = $xpath->query($query);
    if(!empty($title_contents)){
      $title.= $title_contents->item(0)->nodeValue;
    }

    $query = '//*/article//div[contains(@class, \'aksa-articleBody\')]';
    $contents = $xpath->query($query);
    foreach ($contents as $content) {
      $main_description .= $dom->saveHTML($content);
    }
    
    $input['title'] = $title;
    $input['image'] = $image_src;
    $input['live_link'] = $item_url;
    $input['category'] = $cat_array;
    $input['main_description'] = $main_description;
    return $input;
  }

  public function getSpaContent($item_url,$date){
    $input = $cat_array = [];
    $title = $main_description = $image_src = '';
    $data = $this->getDomContent($item_url);
    $dom = new \DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new \DOMXPath($dom);

    $query = '//*/div[starts-with(@class, \'site-content\')]//div[starts-with(@class, \'block_home_slider\')]//h4';
    $title_contents = $xpath->query($query);
    if(!empty($title_contents)){
      $title.= $title_contents->item(0)->nodeValue;
    }

    $query = '//*/div[starts-with(@class, \'divNewsDetailsText\')]';
    $contents = $xpath->query($query);
    foreach ($contents as $content) {
      $main_description .= $dom->saveHTML($content);
    }
    $main_description = str_replace("cashdisk/barcode","http://www.spa.gov.sa/cashdisk/barcode",$main_description);
    $input['title'] = $title;
    $input['image'] = $image_src;
    $input['live_link'] = $item_url;
    $input['category'] = $cat_array;
    $input['main_description'] = $main_description;
    return $input;
  }
}
