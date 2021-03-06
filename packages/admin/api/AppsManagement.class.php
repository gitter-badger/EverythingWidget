<?php

namespace admin;

use Module;
use EWCore;

/**
 * Description of SectionManagement
 *
 * @author Eeliya
 */
class AppsManagement extends \ew\Module
{

   protected $resource = "api";

   public function get_title()
   {
      return "Apps";
   }

   public function get_app_sections($appDir)
   {
      /*//$tempEW = new EWCore($appDir . '/');

      $path = EW_PACKAGES_DIR . '/' . $appDir . '/api/';

      $section_dirs = opendir($path);
      $sections = array();

      // Search app's root's dir
      while ($section_dir = readdir($section_dirs))
      {
         if (strpos($section_dir, '.') === 0)
            continue;
         //$i = strpos($section_dir, '.class.php');
         $section_class_name = substr($section_dir, 0, strpos($section_dir, '.class.php'));
         $class_name = $section_class_name;
         $namespace_class_name = $appDir . "\\" . $section_class_name;
         //echo $namespace_class_name . "<br>";
         if (class_exists($namespace_class_name))
         {
            $section_class_name = $namespace_class_name;
         }

         if (class_exists($section_class_name) && get_parent_class($section_class_name) == 'ew\Module')
         {
            $sc = new $section_class_name($this->get_app());
            //echo $appDir." ".$class_name;
            $permission_id = EWCore::does_need_permission($appDir, $class_name, $sc->get_index());
            //var_dump($_SESSION['EW.USER_ID'])."<br>";
            if ($permission_id && $permission_id !== FALSE)
            {
               // Check for user permission

               if (!UsersManagement::user_has_permission($appDir, $class_name, $permission_id, $_SESSION['EW.USER_ID']))
               {
                  continue;
               }
            }

            if ($sc->get_title() && !$sc->is_hidden())
               $sections[] = array(
                   "title" => "tr:$appDir" . "{" . $sc->get_title() . "}",
                   "className" => $class_name,
                   "description" => "tr:$appDir" . "{" . $sc->get_description() . "}");
         }
      }
      return ($sections);*/
      
      $app_class_name = $appDir . '\\App';
      if (class_exists($app_class_name))
         {
            // Create an instance of section with its parent App
            $obj = new $app_class_name;
            return json_encode($obj->get_app_api_modules());
         }
      
   }

   /**
    * 
    * @param array $form_config [optional] <p>An array that contains content form configurations.<br/>
    * the keys are: <b>title</b>, <b>saveActivity</b>, <b>updateActivity</b>, <b>data</b>
    * </p>
    * @return string
    */
   public static function create_section_main_form($form_config = null)
   {
      return \EWCore::load_file('admin/html/apps-management/section-main-form.php', $form_config);
   }

   public function get_description()
   {
      return "Your app's Control Panel";
   }

   public function index()
   {
      include '../html/AppsManagement/index.php';
   }

//put your code here
}
