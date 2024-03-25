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

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

/**
 * Class PluginFatherConfig
 */
class PluginFatherConfig extends CommonDBTM
{
   public static $rightname = "plugin_father";

    /**
     * @param bool $update
     * @return null|PluginFatherConfig
     */
   public static function getConfig($update = false) {
       static $config = null;

      if (is_null($config)) {
          $config = new self();
      }
      if ($update) {
          $config->getFromDB(1);
      }
       return $config;
   }

    /**
     * PluginFatherConfig constructor.
     */
   public function __construct() {
       global $DB;

      if ($DB->TableExists($this->getTable())) {
          $this->getFromDB(1);
      }
   }

    /**
     * @param string $interface
     * @return array
     */
   public function getRights($interface = 'central') {
       $values = parent::getRights();

       unset($values[CREATE], $values[DELETE], $values[PURGE]);
       return $values;
   }

   public static function install(Migration $migration) {
       global $DB;

       $table = getTableForItemType(__CLASS__);

      if (!$DB->TableExists($table)) {
          $query = "CREATE TABLE `$table` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`father_ids` TEXT COLLATE utf8_unicode_ci DEFAULT NULL,
				`statut_impacted` TEXT COLLATE utf8_unicode_ci DEFAULT NULL,
				`copy_solution` BOOLEAN NOT NULL DEFAULT '0',
    				`copy_category` BOOLEAN NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
					)
					COLLATE='utf8_unicode_ci'
					ENGINE=InnoDB";
          $DB->query($query) or die($DB->error());

          $query = "INSERT INTO `$table` (`id`, `father_ids`) VALUES
				(1, '[\"0\"]');";

          $DB->query($query) or die($DB->error());
      }
       return true;
   }

   public static function uninstall() {
       $query = "DROP TABLE IF EXISTS `" . getTableForItemType(__CLASS__) . "`";
       return $GLOBALS['DB']->query($query) or die($GLOBALS['DB']->error());
   }

   public function showForm($ID, array $options = []) {
       //$this->initForm($ID, $options);
       $this->getFromDB(1);
       echo "<form name='form' id='".$ID."' method='post' action='" . $this->getFormURL() . "'>";
       echo "<input type='hidden' name='id' value='1'>";

       echo "<div class='center'>";
       echo "<table class='tab_cadre_fixe'>";
       echo '<tbody>';
       echo "<tr><th colspan='2'>" . __("Plugin configuration", "father") . "</th></tr>";

       echo "<tr class='tab_bg_2'>";
       echo "<td id='show_father_td1' width='50%'>";
       echo "<label for='father_ids'>".__("item impacted", "father")."</label>";
       echo "</td>";
       echo "<td >";
       $item_ids = self::getValuesFatherItems();
       Dropdown::showFromArray('father_ids', $item_ids, ['multiple' => true, 'values' => importArrayFromDB($this->fields["father_ids"])]);
       echo "</td>";
       echo "</tr>";

       echo "<tr>";
       echo "<td>";
       echo "<label for='statut_impacted'>".__("Status impacted", "father")."</label>";
       echo "</td>";
       echo "<td >";
       $status_imp = Ticket::getAllStatusArray();
       Dropdown::showFromArray('statut_impacted', $status_imp, ['multiple' => true, 'values' => importArrayFromDB($this->fields["statut_impacted"])]);
       echo "</td>";
       echo "</tr>";

       echo "<tr>";
       echo "<td>";
       echo "<label for='copy_solution'>".__("Copy solution on all ticket's son", "father")."</label>";
       echo "</td>";
       echo "<td >";
       Dropdown::showYesNo("copy_solution", $this->fields['copy_solution']);
       echo "</td>";
       echo "</tr>";

       echo "<tr>";
       echo "<td>";
       echo "<label for='copy_category'>".__("Copy category on all ticket's son", "father")."</label>";
       echo "</td>";
       echo "<td >";
       Dropdown::showYesNo("copy_category", $this->fields['copy_category']);
       echo "</td>";
       echo "</tr>";

       echo "<tr class='tab_bg_2'>";
       echo "<td colspan='2' class='center'>";
       echo "<input type='submit' name='update' value=\"" . _sx("button", "Post") . "\" class='submit' >";
       echo "</td>";
       echo "</tr>";

       echo '<tbody>';
       echo "</table>";
       echo "</div>";
       Html::closeForm();
   }

   public function isSolutionOk() {
      if (in_array(5, importArrayFromDB($this->fields['statut_impacted'])) && $this->fields['copy_solution']) {
          return true;
      } else {
          return false;
      }
   }

    public function copyCategory() {
      if ($this->fields['copy_category']) {
          return true;
      } else {
          return false;
      }
   }

   public function isStatusImpacted($status) {
       return in_array($status, importArrayFromDB($this->fields['statut_impacted']));
   }

   public static function getValuesFatherItems() {
       $values[0] = __("Ticket");
       //      $values[1] = __("Problem");
       //      $values[2] = __("Change");
       return $values;
   }

    /**
     * @return array
     */
   public function isOk($type) {
       return (array_key_exists('father_ids', $this->fields) && in_array($type, (importArrayFromDB($this->fields['father_ids']))));
   }
}
