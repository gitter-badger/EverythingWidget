<?php/* * title: Image * description: Add an image */?><div class="row mar-top">   <div class="col-xs-12">      <label>         tr{Image}      </label>      <input type="hidden" name="image" alt="Image" data-ew-plugin="image-chooser" style="max-height:400px;">   </div></div><!--<div class="row mar-top">   <div class="btn-group btn-group-justified col-xs-12" data-toggle="buttons">      <label class="btn btn-primary" >         <input type="checkbox" name="resize-original" id="resize-original" value="yes" > tr{Resize Original Image}      </label>   </div></div> --><div class="row mar-top">   <div class="col-xs-6">      <input class="text-field" name="width" data-label="tr{Width}" >   </div>   <div class="col-xs-6">      <input class="text-field" name="height" data-label="tr{Height}" >   </div></div><script>   /*uisWidget.getWidgetData = function ()   {      if ($("#resize-original").is(":checked"))      {      }      else         return {image: $("#image").attr("src")};   };*/</script>