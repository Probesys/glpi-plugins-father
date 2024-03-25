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

include('../../../inc/includes.php');

if (! isset($_GET["id"])) {
   $_GET["id"] = 0;
}
//Check les droits
Session::checkRight("config", CREATE);
$plugin = new Plugin();
if ($plugin->isActivated("father")) {
    $config = new PluginFatherConfig();

   if (isset($_POST["update"])) {
      if (isset($_POST['father_ids'])) {
          $_POST['father_ids'] = exportArrayToDB($_POST['father_ids']);
      } else {
          $_POST['father_ids'] = exportArrayToDB([]);
      }
      if (isset($_POST['statut_impacted'])) {
          $_POST['statut_impacted'] = exportArrayToDB($_POST['statut_impacted']);
      } else {
          $_POST['statut_impacted'] = exportArrayToDB([]);
      }
       Session::checkRight("config", UPDATE);
       $config->update($_POST);
       //Update singelton
       PluginFatherConfig::getConfig(true);
       Html::redirect($_SERVER['HTTP_REFERER']);
   } else {
       Html::header(PluginFatherConfig::getTypeName(), '', "plugins", "father");
       $config->showForm("fatherConfig");
       Html::footer();
   }
} else {
    Html::header(__('Setup'), '', "config", "plugins");
    echo "<div align='center'><br><br>";
    echo "<img src=\"" . $CFG_GLPI["root_doc"] . "/pics/warning.png\" alt='warning'><br><br>";
    echo "<b>" . __('Please activate the plugin', 'father') . "</b></div>";
    Html::footer();
}
