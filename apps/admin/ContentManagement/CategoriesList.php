<?phpsession_start();if (!$_SESSION['login']){  return;}$parentId = mysql_real_escape_string($_POST['parentId']);$preParentId = '-1';$result = $db->query("SELECT * FROM content_categories WHERE id = '$parentId'") or die(mysql_error());while ($row = $result->fetch_assoc()){  $preParentId = $row['parent_id'];}$pageNumber = mysql_real_escape_string($_POST['pageNumber']);$limit = 30;if ($_REQUEST['cmd'] == 'ArticlesList'){  $pageNumber = (int) $pageNumber;  if (!$pageNumber)  {    $pageNumber = 1;  }  $start = ($pageNumber - 1) * $limit;  echo pagination("contents WHERE category_id = '$parentId' ORDER BY contents.order", $pageNumber, $limit);  ?>  <table class="contents" >                        <?php    $result = mysql_query("SELECT * FROM contents                 WHERE category_id = '$parentId'                 ORDER BY contents.order,date_created DESC LIMIT $start,$limit") or die(mysql_error());    $rowNum = ++$start;    while ($row = mysql_fetch_array($result, MYSQLI_ASSOC))    {      ?>      <tr>        <td>          <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="seeArticleInfo(<?php echo $row['id']; ?>)" >            <span></span>            <p><?php echo $row['title']; ?></p>            <p class="date"><?php echo $row['date_created']; ?></p>          </div>        </td>        <?php        $row = mysql_fetch_array($result);        if ($row)        {          ?>          <td>            <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="seeArticleInfo(<?php echo $row['id']; ?>)" >              <span></span>              <p><?php echo $row['title']; ?></p>              <p class="date"><?php echo $row['date_created']; ?></p>            </div>          </td>          <?php        }        else        {          ?>          <td>            &nbsp;          </td>          <?php        }        $row = mysql_fetch_array($result);        if ($row)        {          ?>          <td>            <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="seeArticleInfo(<?php echo $row['id']; ?>)" >              <span></span>              <p><?php echo $row['title']; ?></p>              <p class="date"><?php echo $row['date_created']; ?></p>            </div>          </td>          <?php        }        else        {          ?>          <td>            &nbsp;          </td>          <?php        }        $row = mysql_fetch_array($result);        if ($row)        {          ?>          <td>            <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="seeArticleInfo(<?php echo $row['id']; ?>)" >              <span></span>              <p><?php echo $row['title']; ?></p>              <p class="date"><?php echo $row['date_created']; ?></p>            </div>          </td>          <?php        }        else        {          ?>          <td>            &nbsp;          </td>          <?php        }        $row = mysql_fetch_array($result);        if ($row)        {          ?>          <td>            <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="seeArticleInfo(<?php echo $row['id']; ?>)" >              <span></span>              <p><?php echo $row['title']; ?></p>              <p class="date"><?php echo $row['date_created']; ?></p>            </div>          </td>          <?php        }        else        {          ?>          <td>            &nbsp;          </td>          <?php        }        ?>      </tr>      <?php    }    ?>  </table>  <?php  return;}?><script  type="text/javascript">            <?phpif ($preParentId != -1){  ?>              EW.setHashParameter("preCategoryId", <?php echo $preParentId; ?>);  <?php}else{  ?>              EW.setHashParameter("preCategoryId", null);  <?php}?></script><?phpif ($_REQUEST['cmd'] == 'CategoriesList'){  ?>  <table id="folders-list" class="contents" >                        <?php    $result = $db->query("SELECT * FROM content_categories                         WHERE parent_id = '$parentId'                         ORDER BY content_categories.order") or die(mysql_error());    $rowNum = 1;    $rows = array();    while ($row = $result->fetch_assoc())    {      $rows[] = $row;      ?>      <tr>        <td>          <div class="content-item folder" id="folder-<?php echo $row['id']; ?>" onclick="contentManagement.selectCategory(this, <?php echo $row['id']; ?>)" ondblclick="contentManagement.seeSubCategories();">            <span></span>            <p><?php echo $row['title']; ?></p>                                          </div>        </td>        <?php        $row = mysql_fetch_array($result);        if ($row)        {          ?>          <td>            <div class="content-item folder" id="folder-<?php echo $row['id']; ?>" onclick="contentManagement.selectCategory(this, <?php echo $row['id']; ?>)" ondblclick="contentManagement.seeSubCategories()">              <span></span>              <p><?php echo $row['title']; ?></p>            </div>          </td>          <?php        }        else        {          ?>          <td>            &nbsp;          </td>          <?php        }        $row = mysql_fetch_array($result);        if ($row)        {          ?>          <td>            <div class="content-item folder" id="folder-<?php echo $row['id']; ?>" onclick="contentManagement.selectCategory(this, <?php echo $row['id']; ?>)" ondblclick="contentManagement.seeSubCategories()">              <span></span>              <p><?php echo $row['title']; ?></p>            </div>          </td>          <?php        }        else        {          ?>          <td>            &nbsp;          </td>          <?php        }        $row = mysql_fetch_array($result);        if ($row)        {          ?>          <td>            <div class="content-item folder" id="folder-<?php echo $row['id']; ?>" onclick="contentManagement.selectCategory(this, <?php echo $row['id']; ?>)" ondblclick="contentManagement.seeSubCategories()">              <span></span>              <p><?php echo $row['title']; ?></p>            </div>          </td>          <?php        }        else        {          ?>          <td>            &nbsp;          </td>          <?php        }        $row = mysql_fetch_array($result);        if ($row)        {          ?>          <td>            <div class="content-item folder" id="folder-<?php echo $row['id']; ?>" onclick="contentManagement.selectCategory(this, <?php echo $row['id']; ?>)" ondblclick="contentManagement.seeSubCategories()">              <span></span>              <p><?php echo $row['title']; ?></p>            </div>          </td>          <?php        }        else        {          ?>          <td>            &nbsp;          </td>          <?php        }        ?>      </tr>      <?php    }    ?>  </table>  <script  type="text/javascript">            /*$.each(<?php echo json_encode($rows); ?>, function(key, val)             {             var td = document.createElement("");             alert(key + "    " + val.title);             });*/  </script>  <?php  if ($preParentId != -1 && $parentId != $preParentId)  {    ?>    <div id="articlesList" style="width:100%;float:left;">      <?php      $pageNumber = (int) $pageNumber;      if (!$pageNumber)      {        $pageNumber = 1;      }      $start = ($pageNumber - 1) * $limit;      echo pagination("contents WHERE category_id = '$parentId' ORDER BY contents.order", $pageNumber, $limit);      ?>      <table class="contents" >                            <?php        $result = mysql_query("SELECT * FROM contents WHERE category_id = '$parentId' ORDER BY contents.order, date_created DESC LIMIT $start,$limit") or die(mysql_error());        $rowNum = 4;        while ($row = mysql_fetch_array($result))        {          ?>          <tr>            <td>              <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="EW.setHashParameter('cmd', 'see');" >                <span></span>                <p><?php echo $row['title']; ?></p>                <p class="date"><?php echo $row['date_created']; ?></p>              </div>            </td>            <?php            $row = mysql_fetch_array($result);            if ($row)            {              ?>              <td>                <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="EW.setHashParameter('cmd', 'see');" >                  <span></span>                  <p><?php echo $row['title']; ?></p>                  <p class="date"><?php echo $row['date_created']; ?></p>                </div>              </td>              <?php            }            else            {              ?>              <td>                &nbsp;              </td>              <?php            }            $row = mysql_fetch_array($result);            if ($row)            {              ?>              <td>                <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="EW.setHashParameter('cmd', 'see');" >                  <span></span>                  <p><?php echo $row['title']; ?></p>                  <p class="date"><?php echo $row['date_created']; ?></p>                </div>              </td>              <?php            }            else            {              ?>              <td>                &nbsp;              </td>              <?php            }            $row = mysql_fetch_array($result);            if ($row)            {              ?>              <td>                <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="EW.setHashParameter('cmd', 'see');" >                  <span></span>                  <p><?php echo $row['title']; ?></p>                  <p class="date"><?php echo $row['date_created']; ?></p>                </div>              </td>              <?php            }            else            {              ?>              <td>                &nbsp;              </td>              <?php            }            $row = mysql_fetch_array($result);            if ($row)            {              ?>              <td>                <div class="content-item article" onclick="contentManagement.selectArticle(this, <?php echo $row['id']; ?>)" ondblclick="EW.setHashParameter('cmd', 'see');" >                  <span></span>                  <p><?php echo $row['title']; ?></p>                  <p class="date"><?php echo $row['date_created']; ?></p>                </div>              </td>              <?php            }            else            {              ?>              <td>                &nbsp;              </td>              <?php            }            ?>          </tr>          <?php        }        ?>      </table>    </div>    <script  type="text/javascript">            function listArticles(query, pageNumber)            {              $("#articlesList").html("<span class='LoadingAnimation'></span>");              $.post('ContentManagement/CategoriesList.php', {cmd: "ArticlesList", parentId:<?php echo $parentId ?>, query: query, pageNumber: pageNumber}, function(data)              {                $("#articlesList").html(data);                setTimeout(function() {                  $(".contentItem").addClass("ready");                }, 100);              });            }            $("#query").keypress(function(e)            {              if (e.which == 13)              {                listArticles($("#query").val(), $("#pageNumber").val());              }            });            function pageChanged(pageNum)            {              listArticles($("#query").val(), pageNum);            }    </script>    <?php    return;  }}?>