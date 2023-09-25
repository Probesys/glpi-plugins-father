<?php
/**
 * ---------------------------------------------------------------------
 *  father is a plugin to add relation father / son between tickets
 *  ---------------------------------------------------------------------
 *  LICENSE
 *
 *  This file is part of father.
 *
 *  father is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  father is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with father. If not, see <http://www.gnu.org/licenses/>.
 *  ---------------------------------------------------------------------
 *  @copyright Copyright © 2022-2023 probeSys'
 *  @license   http://www.gnu.org/licenses/agpl.txt AGPLv3+
 *  @link      https://github.com/Probesys/glpi-plugins-father
 *  @link      https://plugins.glpi-project.org/#/plugin/father
 *  ---------------------------------------------------------------------
 */

// Plugin hook after *Uninstall*
function plugin_uninstall_after_father($item) {
    $fatheritem = new PluginFatherFatherItem();
    $fatheritem->deleteByCriteria(['itemtype' => $item->getType(), 'items_id' => $item->getID()]);
}
function plugin_father_install() {
    $version   = plugin_version_father();
    $migration = new Migration($version['version']);

    // Parse inc directory
   foreach (glob(dirname(__FILE__).'/inc/*') as $filepath) {
       // Load *.class.php files and get the class name
      if (preg_match("/inc.(.+)\.class.php/", $filepath, $matches)) {
          $classname = 'PluginFather' . ucfirst($matches[1]);
          include_once($filepath);
          // If the install method exists, load it
         if (method_exists($classname, 'install')) {
            $classname::install($migration);
         }
      }
   }

    return true;
}

function plugin_father_uninstall() {
    // Parse inc directory
   foreach (glob(dirname(__FILE__).'/inc/*') as $filepath) {
       // Load *.class.php files and get the class name
      if (preg_match("/inc.(.+)\.class.php/", $filepath, $matches)) {
          $classname = 'PluginFather' . ucfirst($matches[1]);
          include_once($filepath);
          // If the install method exists, load it
         if (method_exists($classname, 'uninstall')) {
            $classname::uninstall();
         }
      }
   }
    return true;
}




////// SEARCH FUNCTIONS ///////() {

// Define search option for types of the plugins
/**
 * @param $itemtype
 * @return array
 */
function plugin_father_getAddSearchOptions($itemtype) {
    $sopt = [];

   if ($itemtype == "Ticket") {
       $rng1 = PluginFatherFather::TAG_SEARCH_NUM;
       $sopt[$rng1]['table'] = 'glpi_plugin_father_fathers';
       $sopt[$rng1]['field'] = 'isfather';
       $sopt[$rng1]['name'] = __('Father type', 'father');
       $sopt[$rng1]['datatype'] = "bool";
       $sopt[$rng1]['joinparams']    = ['jointype' => "itemtype_item"];
       $sopt[$rng1]['massiveaction'] = false;
       return $sopt;
   }
}
