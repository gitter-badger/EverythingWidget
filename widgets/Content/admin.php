<?php/* * title: Content * description: Show an article, app page or select a page feeder * feeder_type: page */session_start();?><div class="row mar-top">  <div class="col-xs-12 ">          <input class="text-field" name="feeder" id="feeder" data-label="Default Content" data-ew-plugin="link-chooser" >      </div>  <div class="btn-group col-xs-12" data-toggle="buttons">    <label class="btn btn-primary btn-sm pull-right" >      <input type="checkbox" name="priority-with-url" id="priority-with-url" value="yes" > Priority With URL    </label>  </div></div> <div class="row">  <div class="col-xs-12">    <input class="text-field"  name="linkAddress" id="linkAddress" data-label="Link Address" data-ew-plugin="link-chooser">  </div></div><div class="row">  <div class="col-xs-12">    <input class="text-field" id="linkName" name="linkName" data-label="Link Title" >  </div></div><div class="row">  <div class="col-xs-12">    <select class="" id="animation" name="animation" onchange="" data-label="Animation">      <option value="0">        tr{None}      </option>      <option value="1">        tr{Fade}      </option>           <option value="2">        tr{Height Slide}      </option>         </select>  </div></div><div class="row">  <div class="col-xs-12 mar-top" >    <label>      Title    </label>    <div class="btn-group btn-group-justified margin-bottom" data-toggle="buttons">      <label class="btn btn-default active">        <input type="radio" name="title" id="col-hidden" value="false" checked="true"> hide      </label>      <label class="btn btn-default ">        <input type="radio" name="title" id="col-hidden" value="h1"> H1      </label>      <label class="btn btn-default ">        <input type="radio" name="title" id="col-hidden" value="h2"> H2      </label>      <label class="btn btn-default ">        <input type="radio" name="title" id="col-hidden" value="h3"> H3      </label>      <label class="btn btn-default ">        <input type="radio" name="title" id="col-hidden" value="h4"> H4      </label>      <label class="btn btn-default ">        <input type="radio" name="title" id="col-hidden" value="h5"> H5      </label>      <label class="btn btn-default ">        <input type="radio" name="title" id="col-hidden" value="h6"> H6      </label>    </div>  </div></div><script  type="text/javascript">  var d;  var element = null;  function selectLink(elmId)  {    element = elmId;    d = EW.createModal();    $.post("/admin/ContentManagement/file-chooser.php", {callback: "fileChooserCallbck"}, function(data) {      d.html(data);    });  }  function fileChooserCallbck(rowId)  {    if (element)    {      $(element).val(rowId);      d.dispose();    }  }  function afterSelectPath(elmId)  {    if (d)      d.dispose();    if (elmId == "path")    {    }  }</script>