<?phpsession_start();include($_SESSION['ROOT_DIR'] . '/config.php');include_once 'WidgetsManagementCore.php';if (!$_SESSION['login']){    include($_SESSION['ROOT_DIR'] . '/admin/LoginForm.php');    return;}$pageUISId = mysql_real_escape_string($_POST['pageUISId']);$path = str_replace(',', '&', mysql_real_escape_string($_POST['path']));$uiStructreId = mysql_real_escape_string($_POST['uiStructureId']);if ($_REQUEST['cmd'] == 'Add'){    $result = mysql_query("INSERT INTO pages_ui_structures (path , ui_structure_id) VALUES ('$path' , '$uiStructreId')");    if ($result)    {        ?>        <span class="Title" style="color: #339900;" >            صفحه با موفقیت ثبت شد        </span>        <script  type="text/javascript">                listPages();        </script>        <?php        return;    }    else    {        ?>        <span class="Title" style="color: #dd2200;" >            خطا: صفحه جدید ثبت نشد        </span>        <?php        return;    }}else if ($_REQUEST['cmd'] == 'Edit' && $pageUISId){    $result = mysql_query("UPDATE pages_ui_structures  SET path = '$path' , ui_structure_id = '$uiStructreId' WHERE id = '$pageUISId'");    if ($result)    {        ?>        <span class="Title" style="color: #339900;" >            صفحه با موفقیت ویرایش شد        </span>        <span class="BackButton" onclick="seePageUIS()"></span>        <script  type="text/javascript">                listPages();        </script>        <?php        return;    }    else    {        ?>        <span class="Title" style="color: #dd2200;" >            خطا: صفحه ویرایش نشد        </span>        <?php        return;    }}else if ($_REQUEST['cmd'] == 'Delete' && $pageUISId){    $result = mysql_query("DELETE FROM pages_ui_structures WHERE id = '$pageUISId' AND path NOT LIKE '@%'");    if ($result)    {        ?>        <span class="Title" style="color: #339900;" >            صفحه با موفقیت حذف شد        </span>        <script  type="text/javascript">                listPages();        </script>        <?php        return;    }    else    {        ?>        <span class="Title" style="color: #dd2200;" >            خطا: صفحه حذف نشد        </span>        <?php        return;    }}else if ($_REQUEST['cmd'] == 'See'){    $result = mysql_query("SELECT * FROM pages_ui_structures WHERE id = '$pageUISId'") or die(mysql_error());    while ($row = mysql_fetch_array($result))    {        $path = str_replace('&', ',', $row['path']);        $uisId = $row['ui_structure_id'];        ?>                <span class="Title">            ویرایش ساختار ظاهری صفحه        </span>        <span style="width: 100%; float: right;" id="FormContent">            <?php        }    }    else    {        ?>        <span class="Title">            ساختار ظاهری صفحه        </span>        <span style="width: 100%; float: right;" id="FormContent">            <?php        }        if ($_REQUEST['cmd'] == 'See')        {            ?>            <form action="#" method="POST" onsubmit="return editService()">                <?php            }            else            {                ?>                <form action="#" method="POST" onsubmit="return addService()">                    <?php                }                ?>                 <span class="row">                    <span class="Label">                        صفحه                    </span>                </span>                <span class="row">                    <input class="text-field" value="<?php echo $path ?>" id="path" style="width: 268px; direction: ltr;">                    <span class="button Blue" style="float: right" onclick="selectLink('path')">-</span>                </span>                <span class="row">                    <span class="Label" style="text-align: left;">                        ساختار ظاهری                    </span>                </span>                <span class="row">                    <span class="ComboBox" style="width: 280px;">                        <select id="uiStructureId" >                            <option value="0">---</option>                            <?php                            $result = getUIStructuresList();                            while ($row = mysql_fetch_array($result))                            {                                ?>		                                <option value="<?php echo $row['id']; ?>" <?php echo $row['id'] == $uisId ? 'selected' : '' ?>>                                    <?php echo $row['name']; ?>                                            </option>                                <?php                            }                            ?>                        </select>                    </span>                </span>                <input type="submit" style="display: none;" value="ثبت">                <?php                if ($_REQUEST['cmd'] == 'See')                {                    ?>                    <span class="button green" style="float: left; margin: 10px;" title="ذخیره" onclick="editPage()" >                        ذخیره                    </span>                    <span class="button Red"  onclick="deletePageUIS()" >                        حذف                    </span>                    <?php                }                else                {                    ?>                    <span class="button green" style="float: left; margin: 10px;" title="ثبت" onclick="addPage()" >                        ثبت                    </span>                    <?php                }                ?>            </form>                        <?php            if (!$_REQUEST['cmd'])            {                ?>        </span>        <?php    }    ?>    <script  type="text/javascript">        function addPage()        {            if(!obj('path').value)            {                obj('path').className = 'TextField Red';                return;            }            obj('path').className = 'TextField';            loadPage('WidgetsManagement/SelectPageUISForm.php', 'FormContent', 'cmd=Add&path='+obj('path').value+'&uiStructureId='+obj('uiStructureId').value);            return false;        }                          function editPage()        {            if(!obj('path').value)            {                obj('path').className = 'TextField Red';                return;            }            obj('path').className = 'TextField';            loadPage('WidgetsManagement/SelectPageUISForm.php', 'FormContent', 'cmd=Edit&pageUISId=<?php echo $pageUISId ?>&path='+obj('path').value+'&uiStructureId='+obj('uiStructureId').value);            return false;        }                                                                                 function afterUpload(path)        {            //alert(window.location.href);            obj('iconImage').src = path;            obj('iconImg').value = path;        }                function selectLink()        {            loadPage('Tools/LinkChooser.php', 'TCPC', 'elementId=path');            $("#TCPC").dialog({                width: 800,                height: 440,                title: 'انتخاب صفحه',                modal: true            });        }    </script>