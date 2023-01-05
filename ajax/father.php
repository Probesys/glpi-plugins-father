<?php

include('../../../inc/includes.php');

switch ($_POST['action']) {
    case 'father_values':

        $class = ($_POST['itemtype'] == 'ticket') ? "tab_bg_1" : '';

        echo '<div class="form-field row col-12  mb-2">';
        echo '  <label class="col-form-label col-xxl-4 text-xxl-end" for="father">' . __('Father type', 'father') . '</label>';
        echo '  <div class="col-xxl-8  field-container">';
                PluginFatherFather::fatherYesNo();
        echo '  </div>';
        echo '</div>';

        break;
}
