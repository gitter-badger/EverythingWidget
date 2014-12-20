<?php
session_start();

function sidebar()
{
   // ew-contents-main-form, sidebar
   $html = '<label>tr{Libraries}</label><ul>'
           . '<li><a rel="ajax" href="lib=documents">tr{Documents}</a></li>'
           . '<li><a rel="ajax" href="lib=media">tr{Media}</a></li></ul>';

   return $html;
}

function script()
{
   ob_start();
   ?>
   <script>
      function ContentManagement()
      {
         this.parentId = null;
         this.categoryId = 0;
         this.articleId = 0;
         this.preCategoryId = -1;
         this.oldItem;
         $(window).resize(function () {
            var cn = Math.floor(($("#main-content").width()) / 164);
            var mw = Math.floor(($("#main-content").width() - (cn * 164)) / cn);
            $(".content-item").css("margin-right", mw);
         });
      }
      ContentManagement.prototype.dispose = function ()
      {
      };
      var oldLib = "";
      var contentManagement = new ContentManagement();
      EW.addURLHandler(function () {
         var lib = EW.getHashParameter("lib");
         if (contentManagement.oldLib !== lib) {
            if (contentManagement.currentLib) {
               if (contentManagement.currentLib.dispose) {
                  contentManagement.currentLib.dispose();
               }
               $("#action-bar-items").find("button").remove();
            }
            if (lib == "documents") {
               EW.lock($("#main-content"), "");
               $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/Documents.php', function (data) {
                  EW.unlock($("#main-content"));
                  $("#main-content").html(data);
                  contentManagement.currentLib = documents;
               });
               contentManagement.oldLib = "documents";
            } else {
               EW.lock($("#main-content"), "");
               $.post('<?php echo EW_ROOT_URL; ?>app-admin/ContentManagement/Media.php', function (data) {
                  EW.unlock($("#main-content"));
                  $("#main-content").html(data);
                  contentManagement.currentLib = media;
               });
               contentManagement.oldLib = "media";
            }
         }
         return "ContentManagementHandler";
      });
      if (!EW.getHashParameter("lib"))
         EW.setHashParameter("lib", "documents");
   </script>
   <?php
   return ob_get_clean();
}

EWCore::register_form("ew-app-main-form", "sidebar", ["content" => sidebar()]);
//EWCore::register_form("ew-app-main-form", "content", ["content" => content()]);
echo admin\AppsManagement::create_app_main_form(["script" => script()]);

