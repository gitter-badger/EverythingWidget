<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<form name="apps-plugins-form" id="general-form" >
   <div class="row">
      <?php
      $apps = [];
      $i = 0;
      foreach ($apps as $app)
      {
         $i++;
         //print_r($app);
         ?>
         <div class="col-lg-3 col-md-4 col-sm-6 margin-bottom">
            <div class="box box-white">
               <h2><?php echo $app["name"] ?></h2>
               <div class="row">                                                                           
                  <div class="col-xs-8 mar-bot">
                     <h3>App Root</h3>
                     <label class="value" name="appDir" id="appDir"><?php echo $app["root"] ?></label>
                  </div>
                  <div class="col-xs-4 mar-bot">
                     <h3>Version</h3>
                     <label class="value" name="appDir" id="appDir"><?php echo $app["version"] ?></label>
                  </div>
               </div>
               <div class="row">
                  <div class="col-xs-12" >
                     <h3>Languages</h3>
                  </div>       
               </div>

               <div class="row">
                  <div class="col-xs-12" >
                     <ul class="list indent">
                        <?php
                        $app_root = $app["root"];
                        $app_langs = json_decode(EWCore::get_app_languages($app_root), true);

                        foreach ($app_langs as $key => $lang)
                        {
                           $lang_name = $lang["name"] ? $lang["name"] : "UNDEFINED";
                           echo "<li><a rel='ajax' class='link' href='app=$app_root,lang=$key,form=lang-editor'>$lang_name</a></li>";
                        }
                        ?>
                     </ul>
                  </div>       
               </div>
            </div>
         </div>

         <?php
         // Fix row ordering on different screen size
         if (($i % 4) == 0)
            echo '<div class="row-separator hidden-xs hidden-sm hidden-md"></div>';
         if (($i % 3) == 0)
            echo '<div class="row-separator hidden-xs hidden-sm hidden-lg"></div>';
         if (($i % 2) == 0)
            echo '<div class="row-separator hidden-xs hidden-md hidden-lg"></div>';
      }
      ?>

   </div>
</form>
<script type="text/javascript">
   EW.createModal({hash: {key: "form", value: "lang-editor"}, onOpen: function () {
         EW.lock(this);
         var modal = this;
         var lang = EW.getHashParameter("lang");
         var app = EW.getHashParameter("app");

         $.post("<?php echo EW_ROOT_URL; ?>admin/api/Settings/lanuage-editor-form.php", {app: app, lang: lang}, function (data) {
            modal.html(data);
         });
      },
      onClose: function () {
         EW.setHashParameter("form", null);
         EW.setHashParameter("lang", null);
         EW.setHashParameter("app", null);
      }});
</script>