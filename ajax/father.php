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

switch ($_POST['action']) {
   case 'father_values':

       $class = ($_POST['itemtype'] == 'ticket') ? "tab_bg_1" : '';

       echo '<div class="form-field row col-12  mb-2">';
       echo '  <label class="col-form-label col-xxl-5 text-xxl-end" for="father">' . __('Father type', 'father') . '</label>';
       echo '  <div class="col-xxl-7  field-container">';
               PluginFatherFather::fatherYesNo();
       echo '  </div>';
       echo '</div>';

        break;
}
