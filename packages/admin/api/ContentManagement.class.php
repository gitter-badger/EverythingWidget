<?php

namespace admin;

use EWCore;

/**
 * Description of ContentManagement.
 * 
 * ew-article-form for UI
 * ew-article-action-get, add, update, delete for custom operation for correspondidng action
 * note that custom action is fired after the default action has been done succesfully.
 *
 * @author Eeliya
 */
class ContentManagement extends \ew\Module {

  protected $resource = "api";
  private $file_types = array(
      "jpeg" => "image",
      "jpg"  => "image",
      "png"  => "image",
      "gif"  => "image",
      "txt"  => "text",
      "mp3"  => "sound",
      "mp4"  => "video");
  private $images_resources = array(
      "/is/htdocs/wp1067381_3GN1OJU4CE/www/culturenights/app/webroot/img/logos/");

  protected function install_assets() {
    EWCore::register_app("content-management", $this);
    require_once('models/ew_contents.php');
    require_once('models/ew_contents_labels.php');
    require_once 'asset/DocumentComponent.class.php';
    require_once 'asset/LanguageComponent.class.php';
  }

  protected function install_permissions() {

    ob_start();
    include EW_PACKAGES_DIR . '/admin/html/content-management/link-chooser-document.php';
    $lcd = ob_get_clean();

    ob_start();
    include EW_PACKAGES_DIR . '/admin/html/content-management/link-chooser-document.php';
    $link_chooser_media = ob_get_clean();

    EWCore::register_form("ew/ui/components/link-chooser", "content-chooser", ["title"   => "Contents",
        "content" => $lcd]);

    /* EWCore::register_form("ew/ui/components/link-chooser", "media-chooser", ["title"   => "Media",
      "content" => $link_chooser_media]); */

    EWCore::register_resource("images", array(
        $this,
        "image_loader"));

    $this->register_permission("see-content", "User can see the contents", array(
        'html/index.php',
        'api/index',
        "api/contents",
        "api/content_fields",
        "api/contents_labels",
        "api/get_category",
        "api/get_article",
        "api/albums",
        "api/contents_folders",
        "api/contents_articles",
        "api/get_media_list",
        "api/ew_list_feeder_folder",
        "api/ew_page_feeder_article",
        "html/article-form.php",
        "html/folder-form.php",
        "html/album-form.php"));

    $this->register_permission("manipulate-content", "User can add new, edit, delete contents", array(
        'html/index.php',
        'api/index',
        "api/add_content",
        "api/add_folder",
        "api/add_article",
        "api/add_album",
        "html/upload-form.php",
        "api/update_content",
        "api/update_folder",
        "api/update_article",
        "api/update_album",
        "api/delete_content",
        "api/delete_content_by_id",
        "api/delete_folder",
        "api/delete_article",
        "api/delete_album",
        "api/upload_file",
        "html/article-form.php:tr{New Article}",
        "html/folder-form.php:tr{New Folder}",
        "html/album-form.php:tr{New Album}"));

    //$this->register_content_label("document", ["title" => "Document", "description" => "Attach this content to other content", "type" => "data_url", "value" => "app-admin/ContentManagement/get_articles_llist"]);
    //$this->register_content_label("language", ["title" => "Language", "description" => "Language of the content"]);
    //$this->register_widget_feeder("page", "ssss");
    $this->register_content_component("document", [
        "title"       => "Document",
        "description" => "Main document",
        "explorer"    => "admin/html/content-management/explorer-document.php",
        "explorerUrl" => "~admin/content-management/explorer-document.php",
        "form"        => "admin/html/content-management/label-document.php"
    ]);

    $this->register_content_component("language", [
        "title"       => "Language",
        "description" => "Language of the content",
        "explorer"    => "admin/html/content-management/explorer-language.php",
        "explorerUrl" => "~admin/content-management/explorer-language.php",
        "form"        => "admin/html/content-management/label-language.php"
    ]);

    //$this->register_widget_feeder("page", "article");
    $article_feeder = new \ew\WidgetFeeder("article", $this, "page", "ew_page_feeder_article");
    $article_feeder->title = "article";
    \webroot\WidgetsManagement::register_widget_feeder($article_feeder);

    $folder_feeder = new \ew\WidgetFeeder("folder", $this, "list", "ew_list_feeder_folder");
    $folder_feeder->title = "folder";
    \webroot\WidgetsManagement::register_widget_feeder($folder_feeder);

    //\webroot\WidgetsManagement::register_widget_feeder(new \ew\WidgetFeeder($this, "text", "content_fields"));
    //$this->register_widget_feeder("menu", "languages");
  }

  private function get_node_link($node) {
    $link = null;
    if ($node->nodeName === "a") {
      $link = $node->getAttribute("href");
    }

    return $link;
  }

  private function get_node_src($node) {
    $link = null;
    if ($node->nodeName === "img") {
      $link = $node->getAttribute("src");
    }
    return $link;
  }

  private function get_content_fields($html) {
    $content_fields = new \stdClass();
    if (!isset($html) || $html === "") {
      return $content_fields;
    }
    $dom = new \DOMDocument;
    $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html);
    $xpath = new \DOMXpath($dom);

    $fields = $xpath->query('//*[@content-field]');

    foreach ($fields as $field) {
      $children = $field->childNodes;
      $html = "";
      foreach ($children as $child) {
        $html .= $dom->saveHTML($child);
      }

      $current_field_value = $content_fields->{$field->getAttribute("content-field")};

      if ($current_field_value) {
        if (is_array($current_field_value["content"])) {
          $link = $this->get_node_link($field);
          $field_value = $content_fields->{$field->getAttribute("content-field")};
          $field_value["content"][] = trim($html);

          $field_value["link"][] = $this->get_node_link($field);

          $field_value["src"][] = $this->get_node_src($field);

          $content_fields->{$field->getAttribute("content-field")} = $field_value;
        }
        else {
          $link = $this->get_node_link($field);
          $content_fields->{$field->getAttribute("content-field")} = ["content" => [
                  $current_field_value["content"],
                  trim($html)
              ], "link"    => [
                  $current_field_value["link"],
                  $this->get_node_link($field)
              ],
              "src"     => [
                  $current_field_value["src"],
                  $this->get_node_src($field)
              ]
          ];
        }
      }
      else {
        $link = $this->get_node_link($field);
        $content_fields->{$field->getAttribute("content-field")} = ["content" => trim($html),
            "link"    => $link,
            "src"     => $link];
      }
    }

    return $content_fields;
  }

  public function ew_page_feeder_ssss($id, $language) {
    return null;
  }

  public function image_loader($file) {
    preg_match('/(.*)\.?(\d*)?,?(\d*)?\.([^\.]\w*)/', $file, $match);

    $file = EW_MEDIA_DIR . "/" . $file;
    //Check if the requested url has been matched
    if (count($match) > 0) {
      $real_file_name = EW_MEDIA_DIR . "/" . $match[1] . "." . $match[4];

      // Execute when size has been set and resized file does not exist
      if (!file_exists($file) && $match[2]) {
        // If file is in media dir
        if (file_exists($real_file_name)) {
          //echo count($match);
          $this->create_resized_image($real_file_name, $match[2], $match[3]);
        }
        // If file is another resource dir
        else if (file_exists($this->images_resources[0] . $match[1] . "." . $match[4])) {
          //echo $this->images_resources[0] . $match[1] . "." . $match[4];
          $this->create_resized_image($this->images_resources[0] . $match[1] . "." . $match[4], $match[2], $match[3], false);
        }
      }

      //$file = EW_MEDIA_DIR . "/" . $match[1] . "." . $match[4];
    }
    // If the resized file still does not exist, then the no-image will be sent
    if (!file_exists($file)) {
      //echo urldecode($file);
      //echo $this->images_resources[0] . $match[1] . "." . $match[4];
      if (file_exists($this->images_resources[0] . $match[1] . "." . $match[4])) {
        //echo "h3";
        $file = $this->images_resources[0] . $match[1] . "." . $match[4];
      }
      else {
        $file = EW_PACKAGES_DIR . "/admin/ContentManagement/no-image.png";
        /* $apps_dir = opendir("/is/htdocs/wp1067381_3GN1OJU4CE/www/culturenights/app/webroot/img/logos/");
          while ($app_root = readdir($apps_dir))
          {
          echo $app_root . "<br>";
          } */

        //echo "404 NOT FOUND ".$file;
        //return;
      }
    }
    //echo headers_sent();
    $path_parts = pathinfo($file);
    $type = 'image/' . $path_parts["extension"];

    $lastModified = filemtime($file);
//get a unique hash of this file (etag)
    $etagFile = md5_file($file);
//get the HTTP_IF_MODIFIED_SINCE header if set
    $ifModifiedSince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
//get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
    $etagHeader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
    if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified && $etagHeader == $etagFile) {
      header("HTTP/1.1 304 Not Modified");
    }
    //set last-modified header
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastModified) . " GMT");
    header("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
    //set etag-header
    header("Etag: $etagFile");
    //make sure caching is turned on
    header('Cache-Control: public');
    header('Content-Type: ' . $type);
    header('Content-Length: ' . filesize($file));
    header('Content-transfer-encoding: binary');
    header('Connection: close');
    //header("Keep-Alive: timeout=5, max=98");
    //echo $path_parts["filename"];
    ob_clean();
    flush();
    readfile($file);
    exit;
  }

  public function update_label($content_id, $key, $value) {
    if (!$content_id)
      EWCore::log_error(400, 'tr{Content Id is requierd}');
    $content = ew_contents::find($content_id)->toArray();

    $value = preg_replace_callback('/\$content\.(\w*)/', function($m) use ($content) {
      return $content[$m[1]];
    }, $value);

    $label = \ew_contents_labels::firstOrNew(['content_id' => $content_id,
                'key'        => $key]);

    if ($value) {
      $label->value = $value;
      $label->save();
    }
    else if ($label->exists) {
      $label->delete();
    }

    return json_encode(["status" => "success",
        "id"     => $label->id]);
  }

  /**
   * 
   * @param type $content_id
   * @return json <p>A list of content labels</p>
   */
  private function get_content_labels($content_id, $key = '%') {
    if (preg_match('/\$content\.(\w*)/', $content_id))
      return [];
    if (!$key)
      $key = '%';
    $labels = \ew_contents_labels::where('content_id', '=', $content_id)->where('key', 'LIKE', $key)->get();
    return $labels;
  }

  public static function contents_labels($content_id, $key, $value = '%') {
    if (preg_match('/\$content\.(\w*)/', $content_id))
      return [];

    if (!$content_id)
      return [];

    if (!$value)
      $value = '%';

    $rows = \ew_contents_labels::join('ew_contents', 'ew_contents_labels.content_id', '=', 'ew_contents.id')
                    ->where(function($query) use ($content_id) {
                      $query->whereIn('content_id', function($query) use ($content_id) {
                        $query->select('content_id')
                        ->from('ew_contents_labels')
                        ->where('content_id', '=', $content_id);
                      })->orWhereIn('content_id', function($query) use ($content_id) {
                        $query->select('content_id')
                        ->from('ew_contents_labels')
                        ->where('key', '=', 'admin_ContentManagement_document')
                        ->where('value', '=', $content_id);
                      });
                    })
                    ->where('key', 'LIKE', $key)
                    ->where('value', 'LIKE', $value)->orderBy('value');
    /* return ["totalRows" => $rows->count(),
      "result" => $rows->get(['*',
      \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")])]; */
    $result = $rows->get([
        '*',
        \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")
    ]);

    $data = array_map(function($e) {
      $e["content_fields"] = json_decode($e["content_fields"], true);
      return $e;
    }, $result->toArray());

    return \ew\APIResourceHandler::to_api_response($data, ["totalRows" => $rows->count()]);
  }

  /**
   * 
   * @param type $type
   * @param type $title
   * @param type $parent_id
   * @param type $keywords
   * @param type $description 
   * @param type $content
   * @param type $featured_image
   * @param string $date_created
   * @param string $date_modified
   * @return JSON json object which hold the result, if the opration is succesful get new row id with "id"
   */
  public function add_content($type, $title, $parent_id, $keywords, $description, $html_content, $featured_image, $labels, $date_created = null, $date_modified = null) {
    $validator = \SimpleValidator\Validator::validate(compact(['title',
                'type',
                'parent_id']), ew_contents::$rules);
    if (!$validator->isSuccess())
      return EWCore::log_error("400", "tr{Content has not been added}", $validator->getErrors());


    $content = new ew_contents;
    $content->author_id = $_SESSION['EW.USER_ID'];
    $content->type = $type;
    $content->title = $title;
    $content->slug = EWCore::to_slug($title, "ew_contents");
    $content->keywords = $keywords;
    $content->description = $description;
    $content->parent_id = $parent_id;
    $content->content = $html_content;
    if (isset($html_content)) {
      $content->content_fields = json_encode($this->get_content_fields($html_content));
    }
    $content->featured_image = $featured_image;
    $content->date_created = date('Y-m-d H:i:s');
    $content->date_modified = date('Y-m-d H:i:s');
    $content->save();

    if ($content->id) {
      $id = $content->id;
      $labels = json_decode($labels, true);
      if (is_array($labels))
        foreach ($labels as $key => $value) {
          $this->update_label($id, $key, $value);
        }
    }
    return \ew\APIResourceHandler::to_api_response($content->toArray());
  }

  public function update_content($id, $title, $type, $parent_id, $keywords, $description, $html_content, $featured_image, $labels) {
    $validator = \SimpleValidator\Validator::validate(compact(['title',
                'type',
                'parent_id']), ew_contents::$rules);
    if (!$validator->isSuccess())
      return EWCore::log_error("400", "tr{Content has not been added}", $validator->getErrors());

    $content = ew_contents::find($id);
    $content->author_id = $_SESSION['EW.USER_ID'];
    $content->type = $type;
    $content->title = $title;
    $content->slug = EWCore::to_slug($title, "ew_contents");
    $content->keywords = $keywords;
    $content->description = $description;
    $content->parent_id = $parent_id;
    $content->content = $html_content;
    if (isset($html_content)) {
      $content->content_fields = json_encode($this->get_content_fields($html_content));
    }

    $content->featured_image = $featured_image;
    $content->date_modified = date('Y-m-d H:i:s');
    $content->save();

    if ($content->id) {
      $id = $content->id;
      $labels = json_decode($labels, true);
      foreach ($labels as $key => $value) {
        $this->update_label($id, $key, $value);
      }
      return \ew\APIResourceHandler::to_api_response($content->toArray(), [
                  status  => "success",
                  message => "tr{The content has been updated successfully}"
      ]);
    }
    return EWCore::log_error("400", "Something went wrong, content has not been updated");
  }

  public function add_article($title, $parent_id, $keywords, $description, $labels) {
    if (!$parent_id)
      $parent_id = 0;

    $htmlContent = $_REQUEST['content'];

    if (!$title) {
      \EWCore::log_error(400, "tr{Title is requierd}");
    }

    $result = $this->add_content("article", $title, $parent_id, $keywords, $description, $htmlContent, "", $labels);

    if ($result["data"]["id"]) {

      return \ew\APIResourceHandler::to_api_response($this->get_content_by_id($result["data"]["id"])["data"], [
                  "message" => "tr{The new article has been added succesfully}",
                  "status"  => "success"
      ]);
      // End of plugins actions call
    }
    return $result;
//      return \EWCore::log_error(400, "tr{Something went wrong, content has not been added}");
  }

  public function ew_page_feeder_article($id, $language = "en") {

    $articles = $this->contents_labels($id, "admin_ContentManagement_language", $language);
    $article = [];

    if ($articles) {
      $article = $articles["data"][0];
      //$result["html"] = "WIDGET_DATA_MODEL";
      $result["title"] = $article["title"];
      $result["content"] = $article["content"];
      $result["content_fields"] = $article["content_fields"];
      return \ew\APIResourceHandler::to_api_response($result, ["type" => "object"]);
    }

    return \ew\APIResourceHandler::to_api_response([]);
  }

  public function ew_list_feeder_folder($id, $token = 0, $size, $order_by = null) {
    if (!$token)
      $token = 0;
    if (!$size)
      $size = 30;

    $articles = $this->contents_articles($id, $token, $size, $order_by);

    $result = [];
    if (isset($articles["data"])) {
      foreach ($articles["data"] as $article) {
        $result[] = ["html" => "{$article["content"]}"];
      }
    }
    return \ew\APIResourceHandler::to_api_response($result, ["totalRows" => $articles["totalRows"]]);
  }

  public function ew_menu_feeder_languages($id, $token = 0, $size) {
    if (!$token)
      $token = 0;
    if (!$size)
      $size = 30;

    return ['title' => ['link' => '',
            'icon' => '']];
  }

  public function ew_menu_feeder_cp_languages($parameters) {
    //$this->v
  }

  public function get_article($articleId) {
    //echo "$articleId";
    if (!$articleId) {
      return EWCore::log_error(400, 'tr{Article Id is requierd}');
    }
    $article = ew_contents::find($articleId, ['*',
                \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);
    if (!isset($article)) {
      return \EWCore::log_error(404, "Requested article not found", "article is not exist: $articleId");
    }
    $article = $article->toArray();
    $article["labels"] = $this->get_content_labels($articleId);
    return \ew\APIResourceHandler::to_api_response($article);
  }

  public function update_article($id, $title, $parent_id, $keywords = null, $description = null, $content = null, $labels = null) {
    $v = new \Valitron\Validator($this->get_current_command_args());


    $v->rule('required', ["title",
        "parent_id"])->message(' {field} is required');
    $v->rule('integer', "parent_id")->message(' {field} should be integer');
    $v->labels(array(
        "title"     => 'tr{Title}',
        "parent_id" => 'Folder ID'
    ));
    if (!$v->validate())
      return EWCore::log_error("400", "tr{New article has not been added}", $v->errors());

    $result = $this->update_content($id, $title, 'article', $parent_id, $keywords, $description, $content, null, $labels);

    if ($result["statusCode"] === 200) {
      $result["message"] = "tr{Article has been updated successfully}";
      return $result;
    }
    else {
      return EWCore::log_error("400", "Article has not been updated");
    }
  }

  public function contents_folders($parent_id, $token, $size) {
    $container_id = ew_contents::find($parent_id);
    $up_parent_id = $container_id['parent_id'] ? $container_id['parent_id'] : 0;
    //$container_id = $container_id['parent_id'];
    $folders = ew_contents::where('parent_id', '=', $parent_id)->where('type', 'folder')->get(['*',
        \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);

    $rows = array();
    $folders_ar = $folders->toArray();

    foreach ($folders_ar as $i) {
      $i["up_parent_id"] = $up_parent_id;
      $rows[] = $i;
    }

    return \ew\APIResourceHandler::to_api_response($rows, [
                "totalRows" => $folders->count(),
                "parent"    => isset($container_id) ? $container_id : []
    ]);
  }

  public function contents_articles($parent_id = null, $token, $size, $order_by = null) {
    if (!isset($token)) {
      $token = 0;
    }
    if (!$size) {
      $size = '18446744073709551610';
    }

    // if there is no parent_id then select all the articles
    if (is_null($parent_id) && $parent_id != 0) {
      $articles = ew_contents::where('type', 'article')->orderBy('title')->get(['*',
          \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);

      $data = array_map(function($e) {
        $e["content_fields"] = json_decode($e["content_fields"], true);
        return $e;
      }, $articles->toArray());

      return \ew\APIResourceHandler::to_api_response($data, ["totalRows" => $articles->count()]);
    }
    else {
      $container_id = ew_contents::find($parent_id);
      $up_parent_id = isset($container_id['parent_id']) ? $container_id['parent_id'] : 0;
      if (isset($order_by)) {
        $articles = ew_contents::where('parent_id', '=', $parent_id)
                ->where('type', 'article')
                ->take($size)
                ->skip($token)
                ->orderBy("date_modified", $order_by)
                ->get(['*',
            \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);
      }
      else {
        $articles = ew_contents::where('parent_id', '=', $parent_id)->where('type', 'article')->take($size)->skip($token)->get(['*',
            \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);
      }

      $data = array_map(function($e) use ($up_parent_id) {
        $e["up_parent_id"] = $up_parent_id;
        $e["content_fields"] = json_decode($e["content_fields"], true);
        return $e;
      }, $articles->toArray());

      return \ew\APIResourceHandler::to_api_response($data, ["totalRows" => $articles->count()]);
    }

    return \EWCore::log_error(400, 'tr{Something went wrong}');
  }

  public function contents($_parts__id, $title_filter, $type, $token, $size) {
    if (isset($_parts__id)) {
      return $this->get_content_by_id($_parts__id);
    }
    else {
      return $this->get_contents($title_filter, $type, $token, $size);
    }
  }

  public function content_fields($_parts__id, $language) {
    $content = $this->get_content_by_id($_parts__id, $language);

    return \ew\APIResourceHandler::to_api_response($content["data"]["content_fields"]);
  }

  private function get_content_by_id($id, $language = ën) {
    if (!isset($id))
      return \EWCore::log_error(400, 'tr{Content Id is requird}');
    $content = ew_contents::find($id, ['*',
                \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);

    if (isset($content)) {
      $content->content_fields = json_decode($content->content_fields, true);

      $labels = $this->get_content_labels($id);
      $content->labels = $labels;
      return \ew\APIResourceHandler::to_api_response($content->toArray());
    }

    return EWCore::log_error(404, "content not found");
  }

  private function get_contents($title_filter = '%', $type = '%', $token = 0, $size = 99999999999999) {
    if (!$token) {
      $token = 0;
    }

    if (!$size) {
      $size = '18446744073709551610';
    }

    $contents = ew_contents::where('type', 'LIKE', $type)
                    ->where(\Illuminate\Database\Capsule\Manager::raw("`title` COLLATE UTF8_GENERAL_CI"), 'LIKE', $title_filter . '%')
                    ->orderBy('title')->take($size)->skip($token)->get(['*',
        \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")]);

    $data = array_map(function($e) {
      $e["content_fields"] = json_decode($e["content_fields"], true);
      return $e;
    }, $contents->toArray());

    return \ew\APIResourceHandler::to_api_response($data, ["totalRows" => $contents->count()]);
  }

  public function add_folder($title, $parent_id, $keywords, $description, $labels) {
    $db = \EWCore::get_db_connection();

    if (!$parent_id)
      $parent_id = 0;

    $html_content = $_REQUEST['content'];

    $result = $this->add_content("folder", $title, $parent_id, $keywords, $description, $html_content, "", $labels);
    //$result = json_decode($result, true);

    if ($result['data']["id"]) {
      $content_id = $result['data']["id"];
      $res = [
          "status"  => "success",
          "message" => "Folder has been added successfully",
          "data"    => [
              "id"   => $content_id,
              "type" => "folder"
          ]
      ];

      return \ew\APIResourceHandler::to_api_response($res);
    }
    return $result;
  }

  public function get_category($id) {
    $db = \EWCore::get_db_connection();

    $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE id = '$id'") or die($db->error);

    if ($rows = $result->fetch_assoc()) {
      $db->close();
      return json_encode($rows);
    }
  }

  public function update_folder($id = null, $title = null, $parent_id = null, $keywords = null, $description = null, $content = null, $labels = null) {
    $db = \EWCore::get_db_connection();

    //$createdModified = date('Y-m-d H:i:s');
    $v = new \Valitron\Validator($this->get_current_command_args());
    //print_r(json_decode(stripslashes($labels), TRUE));
    //echo $parent_id;
    //global $functions_arguments;
    //print_r($this->get_current_method_args());
    //$db = \EWCore::get_db_connection();
    //print_r(func_get_args());     
    $v->rule('required', ["title",
        "parent_id"])->message(' {field} is required');
    $v->rule('integer', "parent_id")->message(' {field} should be integer');
    $v->labels(array(
        "title"     => 'tr{Title}',
        "parent_id" => 'Folder ID'
    ));
    /* $id = $db->real_escape_string($_REQUEST['id']);
      $title = $db->real_escape_string($_REQUEST['title']);
      $parent_id = $db->real_escape_string($_REQUEST['parent_id']);
      $keywords = $db->real_escape_string($_REQUEST['keywords']);
      $description = $db->real_escape_string($_REQUEST['description']); */
    if (!$v->validate())
      return EWCore::log_error("400", "New folder has not been added", $v->errors());

    /* $content = (stripcslashes($content));
      $createdModified = date('Y-m-d H:i:s');
      $stm = $db->prepare("UPDATE ew_contents
      SET title = ?
      , keywords = ?
      , description = ?
      , parent_id = ?
      , content = ?
      , date_modified = ? WHERE id = ?");
      $stm->bind_param("sssssss", $title, $keywords, $description, $parent_id, $content, $createdModified, $id); */
    $result = $this->update_content($id, $title, 'folder', $parent_id, $keywords, $description, $content, null, $labels);

    if ($result["statusCode"] === 200) {
      $result["message"] = "tr{Folder has been updated successfully}";
      return $result;
    }
    else {
      return EWCore::log_error("400", "New folder has not been added", $db->error_list);
    }
  }

  public function delete_image($id) {
    $db = \EWCore::get_db_connection();
    if (!$id)
      $id = $db->real_escape_string($_REQUEST["id"]);
    $result = $db->query("SELECT * FROM ew_contents WHERE parent_id = '$id' LIMIT 1");
    if ($result->fetch_assoc()) {
      return json_encode(array(
          status      => "unable",
          status_code => 2));
      return;
    }
    $result = $db->query("SELECT * FROM ew_contents, ew_images WHERE ew_contents.id = ew_images.content_id AND ew_contents.id = '$id' LIMIT 1");
    if ($file = $result->fetch_assoc()) {
      $path_parts = pathinfo(EW_MEDIA_DIR . '/' . $file["source"]);
      unlink(EW_MEDIA_DIR . '/' . $path_parts["basename"]);
      unlink(EW_MEDIA_DIR . '/' . $path_parts["filename"] . '.thumb.' . $path_parts["extension"]);
    }
    $result = $db->query("DELETE FROM ew_contents WHERE type = 'image' AND id = '$id'");
    $db->close();
    if ($result) {
      return json_encode(array(
          "status"      => "success",
          "status_code" => 1,
          "message"     => ""));
    }
    else {
      return json_encode(array(
          "status"      => "unsuccess",
          "status_code" => 0,
          "message"     => ""));
    }
  }

  public function delete_content($id) {
    $db = \EWCore::get_db_connection();

    $items = ew_contents::where("parent_id", $id)->get();
    if ($items->count() > 0) {
      //return array(status => "unable", status_code => 2);
      return \EWCore::log_error(400, "tr{In order to delete this folder, you must delete content of this folder first}");
    }

    if (ew_contents::destroy($id)) {
      return \ew\APIResourceHandler::to_api_response(["status"      => "success",
                  "status_code" => 1,
                  "message"     => "Content has been deleted successfully"]);
    }
    else {
      return \EWCore::log_error(400, "tr{Something went wrong, please try again}");
    }
  }

  public function delete_album($id) {
    $result = $this->delete_content($id);
    if ($result["status_code"] == 1) {
      $result["message"] = "The album has been deleted successfuly";
    }
    else if ($result["status_code"] == 2) {
      $result["message"] = "Unable to delete the album";
    }
    else {
      $result["message"] = "Album has NOT been deleted";
    }
    return $result;
  }

  public function delete_folder($id) {
    return $this->delete_content($id);
  }

  public function delete_article($id) {
    return $this->delete_content($id);
  }

  public function get_documents_list($parentId, $token = null, $size = null) {
    $db = \EWCore::get_db_connection();

    if (!isset($token)) {
      $token = 0;
    }
    if (!$size) {
      $size = 99999999999999;
    }

    $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM content_categories WHERE parent_id = '$parentId' ORDER BY title") or die("safasfasf");
    $categories = array();
    while ($r = $result->fetch_assoc()) {
      $r["document_type"] = "folder";
      $categories[] = $r;
    }

    $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE category_id = '$parentId' ORDER BY title") or die("safasfasf");
    $articles = array();
    while ($r = $result->fetch_assoc()) {
      $r["document_type"] = "article";
      $articles[] = $r;
    }
    $documents = array_merge($categories, $articles);
    $db->close();
    $out = array(
        "totalRows" => count($documents),
        "result"    => $documents);
    return json_encode($out);
  }

  public function get_title() {
    return "Content";
  }

  public function get_description() {
    return "Manage the content of your website. Add new artile, Edit or Delete exiting article";
  }

  function createThumbs($pathToImages, $pathToThumbs, $thumbWidth) {
    // open the directory
    $dir = opendir($pathToImages);

    // loop through it, looking for any/all JPG files:
    while (false !== ($fname = readdir($dir))) {
      // parse path for the extension
      $info = pathinfo($pathToImages . $fname);
      // continue only if this is a JPEG image
      if (strtolower($info['extension']) == 'jpg') {
        echo "Creating thumbnail for {$fname} <br />";

        // load image and get image size
        $img = imagecreatefromjpeg("{$pathToImages}{$fname}");
        $width = imagesx($img);
        $height = imagesy($img);

        // calculate thumbnail size
        $new_width = $thumbWidth;
        $new_height = floor($height * ( $thumbWidth / $width ));

        // create a new temporary image
        $tmp_img = imagecreatetruecolor($new_width, $new_height);

        // copy and resize old image into new image 
        imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // save thumbnail into a file
        imagejpeg($tmp_img, "{$pathToThumbs}{$fname}");
      }
    }
    // close the directory
    closedir($dir);
  }

  public function get_media_list($parent_id, $token = null, $size = null) {
    $db = \EWCore::get_db_connection();

    $path = "/";

    $root = EW_MEDIA_DIR;
    $new_width = 140;
    try {
      $files = array();
      $include = ["included" => []];
      // Folder
      $files = ew_contents::where('type', 'album')->where('type', 'album')->where('parent_id', $parent_id)->orderBy('title')->get(['*',
                  \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")])->toArray();
      if (isset($parent_id) && $parent_id !== "0") {
        $include["included"]["album"] = ew_contents::where('type', 'album')->where('id', $parent_id)->orderBy('title')->get(['*',
                    \Illuminate\Database\Capsule\Manager::raw("DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created")])->toArray()[0];
      }
      // images
      $result = $db->query("SELECT *,ew_contents.id AS content_id, DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents, ew_images WHERE ew_contents.id = ew_images.content_id AND ew_contents.parent_id = '$parent_id' ORDER BY title") or die("safasfasf");
      while ($r = $result->fetch_assoc()) {
        //echo "asd";
        $file = $r["source"];
        $file_path = $root . $path . $file;
        $file_info = pathinfo($file_path);

        // create thumb for image if doesn't exist
        $tumbnailURL = 'album-' . $parent_id . $path . $file_info["filename"] . ".thumb." . $file_info["extension"];

        if (!file_exists($file_path)) {
          $files[] = array(
              "id"          => $r["content_id"],
              title         => $r["title"],
              //"parentId" => $container_id,
              type          => $this->file_types[$file_info["extension"]] ? $this->file_types[$file_info["extension"]] : "unknown",
              size          => 0,
              ext           => "unknown",
              url           => 'asset/images' . $path . $file,
              absURL        => EW_ROOT_URL . "asset/images/$file",
              originalUrl   => EW_ROOT_URL . "media/$file",
              filename      => $file_info["filename"],
              fileExtension => $file_info["extension"],
              thumbURL      => "data:image/png;base64,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",
              path          => "data:image/png;base64,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");
          continue;
        }

        list($width, $height) = getimagesize($file_path);
        if (!file_exists($root . $path . $tumbnailURL) && $width > 200) {
          $this->create_image_thumb($file_path, 200);
          $tumbnailURL = 'album-' . $parent_id . $path . $file_info["filename"] . ".thumb." . $file_info["extension"];
        }
        else if ($width <= 200) {
          $tumbnailURL = 'album-' . $parent_id . $path . $file;
        }

        $files[] = [
            "id"          => $r["content_id"],
            title         => $r["title"],
            "parentId"    => $container_id,
            type          => $this->file_types[$file_info["extension"]] ? $this->file_types[$file_info["extension"]] : "unknown",
            size          => round(filesize($file_path) / 1024),
            ext           => $file_info["extension"],
            url           => 'media' . $path . $file,
            absURL        => EW_ROOT_URL . "~rm/public/media/$file",
            originalUrl   => EW_ROOT_URL . "~rm/public/media/$file",
            filename      => $file_info["filename"],
            fileExtension => $file_info["extension"],
            thumbURL      => EW_DIR . '~rm/public/media/' . $tumbnailURL,
            path          => $file_path
        ];
      }
    }
    catch (Exception $e) {
      echo $e->getMessage();
    }
    //var_dump($files);
    return \ew\APIResourceHandler::to_api_response($files, $include);
  }

  public function albums($_parts__id) {
    $db = \EWCore::get_db_connection();


    $result = $db->query("SELECT *,DATE_FORMAT(date_created,'%Y-%m-%d') AS round_date_created FROM ew_contents WHERE id = '$_parts__id'") or die($db->error);

    if ($rows = $result->fetch_assoc()) {
      $db->close();

      return json_encode($rows);
    }
    return json_encode([]);
  }

  public function add_album($title = null, $keywords = NULL, $description = NULL, $html_content = NULL, $labels) {

    $validator = new \Valitron\Validator($this->get_current_command_args());
    $validator->rule('required', ['title']);
    if (!$validator->validate()) {
      return EWCore::log_error(400, 'tr{Form validation error}', $validator->errors());
    }

    $result = $this->add_content("album", $title, 0, $keywords, $description, $htmlContent, "", $labels);
    //$result = json_decode($result, true);
    //$res = array(status => "success", message => "The directory {" . $title . "} hase been created succesfuly");*/
    return json_encode(['status'  => "success",
        'message' => "The directory '$title' hase been created succesfuly",
        'data'    => $result]);
  }

  public function update_album() {
    $db = \EWCore::get_db_connection();
    $albumId = $db->real_escape_string($_REQUEST['id']);
    $title = $db->real_escape_string($_REQUEST['title']);
    $parent_id = $db->real_escape_string($_REQUEST['parent_id']);
    $keywords = $db->real_escape_string($_REQUEST['keywords']);
    $description = $db->real_escape_string($_REQUEST['description']);
    $htmlContent = stripcslashes($_REQUEST['html_content']);
    $createdModified = date('Y-m-d H:i:s');
    $stm = $db->prepare("UPDATE ew_contents 
            SET title = ? 
            , keywords = ? 
            , description = ? 
            , parent_id = ? 
            , content = ? 
            , date_modified = ? WHERE id = ?");
    $stm->bind_param("sssssss", $title, $keywords, $description, $parent_id, $htmlContent, $createdModified, $albumId);

    if ($stm->execute()) {
      $stm->close();
      $db->close();

      echo json_encode(array(
          status => "success",
          title  => $title));
    }
    else {
      echo json_encode(array(
          status => "unsuccess"));
    }
  }

  public function delete_content_by_id($id) {
    $db = \EWCore::get_db_connection();

    if (!$id)
      $id = $db->real_escape_string($_REQUEST["id"]);
    $result = $db->query("SELECT * FROM ew_contents WHERE parent_id = '$id' LIMIT 1");
    $output = array();
    if ($result->fetch_assoc()) {
      return json_encode(array(
          status      => "unable",
          status_code => 2));
    }
    $result = $db->query("DELETE FROM ew_contents WHERE id = '$id'");
    $db->close();
    if ($result) {
      return json_encode(array(
          "status"      => "success",
          "status_code" => 1,
          "message"     => ""));
    }
    else {
      return json_encode(array(
          "status"      => "unsuccess",
          "status_code" => 0,
          "message"     => ""));
    }
    //return json_encode(array("status" => "success", "status_code" => 1, "message" => ""));
  }

  public function create_resized_image($image_path, $width = null, $height = null, $same_path = true) {
    if (!$width && !$height)
      return;
    $src_image = imagecreatefromstring(file_get_contents($image_path));
    $path_parts = pathinfo($image_path);
    $type = $path_parts['extension'];
    //$foo->
    imagealphablending($src_image, true);
    if (!$height || $height == 0)
      $height = floor(imagesy($src_image) * ( $width / imagesx($src_image) ));
    if (!$width || $width == 0)
      $width = floor(imagesx($src_image) * ( $height / imagesy($src_image) ));
    $dst = imagecreatetruecolor($width, $height);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopyresampled($dst, $src_image, 0, 0, 0, 0, $width, $height, imagesx($src_image), imagesy($src_image));
    if (!$same_path) {
      $path_parts['dirname'] = EW_MEDIA_DIR;
    }
    switch ($type) {
      case 'bmp': imagewbmp($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.bmp");
        break;
      case 'gif': imagegif($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.gif");
        break;
      case 'jpg': imagejpeg($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.jpg", 100);
        break;
      case 'jpeg': imagejpeg($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.jpg", 100);
        break;
      case 'png': imagepng($dst, EW_MEDIA_DIR . '/' . $path_parts['filename'] . ".{$width},{$height}.png");
        break;
    }
  }

  public function create_image_thumb($image_path, $width = null, $height = null) {
    if (!$width && !$height)
      return;
    $src_image = imagecreatefromstring(file_get_contents($image_path));
    $path_parts = pathinfo($image_path);
    $type = $path_parts["extension"];
    //$foo->
    imagealphablending($src_image, true);
    if (!$height)
      $height = floor(imagesy($src_image) * ( $width / imagesx($src_image) ));
    if (!$width)
      $width = floor(imagesx($src_image) * ( $height / imagesy($src_image) ));
    $dst = imagecreatetruecolor($width, $height);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopyresampled($dst, $src_image, 0, 0, 0, 0, $width, $height, imagesx($src_image), imagesy($src_image));
    // save thumbnail into a file
    //imagepng($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . '.thumb.png', 9, PNG_ALL_FILTERS);
    switch ($type) {
      case 'bmp': imagewbmp($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.bmp");
        break;
      case 'gif': imagegif($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.gif");
        break;
      case 'jpg': imagejpeg($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.jpg", 90);
        break;
      case 'jpeg': imagejpeg($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.jpg", 90);
        break;
      case 'png': imagepng($dst, $path_parts['dirname'] . '/' . $path_parts['filename'] . ".thumb.png", 9, PNG_ALL_FILTERS);
        break;
    }
  }

  public function upload_file($path, $parent_id) {
    $db = \EWCore::get_db_connection();
    require_once EW_ROOT_DIR . "core/upload.class.php";
    ini_set("memory_limit", "100M");
    if (isset($_REQUEST["path"]))
      $path = $_REQUEST["path"];

    if (!$parent_id)
      $parent_id = 0;
    $alt_text = $_REQUEST["alt_text"];
    //if (!$order)
    //  $order = 0;


    $root = EW_MEDIA_DIR . '/album-' . $parent_id;
    $succeed = 0;
    $error = 0;
    $thegoodstuf = '';
    $alt_text = "";
    $files = array();
    foreach ($_FILES['img'] as $k => $l) {
      foreach ($l as $i => $v) {
        if (!array_key_exists($i, $files))
          $files[$i] = array();
        $files[$i][$k] = $v;
      }
    }

    foreach ($files as $file) {
      //print_r($file);
      $foo = new \upload($file);
      if ($foo->uploaded) {

        // save uploaded image with no changes
        $foo->Process($root);
        if ($foo->processed) {
          $result = $this->add_content("image", $foo->file_dst_name_body, $parent_id, "", "", "", "", "");
          //$result = json_decode($result, true);
          //print_r($result);
          // $stm = $db->prepare("INSERT INTO ew_contents (title , keywords , description , parent_id , source_page_address , html_content , ew_contents.order , date_created,type) 
          //  VALUES (? , ? , ? , ? , ? , ? , ? , ?,'article')") or die($db->error);
          //  $stm->bind_param("ssssssss", $title, $keywords, $description, $categoryId, $sourcePageAddress, $htmlContent, $order, $createdDate) or die($db->error); 
          //print_r($result);
          if ($result["data"]["id"]) {
            $content_id = $result["data"]["id"];
            $stm = $db->prepare("INSERT INTO ew_images (content_id, source , alt_text) 
            VALUES (? , ? , ?)") or die($db->error);
            $image_path = 'album-' . $parent_id . '/' . $foo->file_dst_name;
            $stm->bind_param("sss", $content_id, $image_path, $alt_text) or die($db->error);
            if ($stm->execute()) {
              $res = array(
                  "status" => "success",
                  "id"     => $stm->insert_id);
              //$stm->close();
              //$db->close();
            }
          }

          $this->create_image_thumb($foo->file_dst_pathname, 200);
          $succeed++;
        }
        else {
          $error++;
        }
      }
      else {
        $error+=2;
      }
    }

    return json_encode(array(
        status  => "success",
        message => "Uploaded: " . $succeed . " Error: " . $error . ' ' . $foo->error));
  }

  /**
   * 
   * @param array $form_config [optional] <p>An array that contains content form configurations.<br/>
   * the keys are: <b>title</b>, <b>saveActivity</b>, <b>updateActivity</b>, <b>data</b>
   * </p>
   * @return string
   */
  public static function create_content_form($form_config = null) {
    return \EWCore::load_file("admin/html/content-management/content-form.php", $form_config);
  }

}
