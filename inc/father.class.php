<?php

class PluginFatherFather extends CommonDBTM
{
    public $dohistory = true;

    const TAG_SEARCH_NUM = 38500;

   public function showForm($ID, $options = []) {
   }

   public static function install(Migration $migration) {
       global $DB;

       $table = getTableForItemType(__CLASS__);

      if (!$DB->TableExists($table)) {
          $query = "CREATE TABLE IF NOT EXISTS `$table` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`isfather` BOOLEAN NOT NULL DEFAULT '0',
				`items_id` INT(11) NOT NULL DEFAULT '1',
				`itemtype` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
				PRIMARY KEY (`id`),
				UNIQUE INDEX `unicity` (`itemtype`, `items_id`, `isfather`)
					)
					COLLATE='utf8_unicode_ci'
					ENGINE=InnoDB";
          $DB->query($query) or die($DB->error());
      }

      if (isIndex($table, "name")) {
          $query = "ALTER TABLE `$table`
				DROP INDEX `name`";
          $DB->query($query) or die($DB->error());
      }

      if (!isIndex($table, "unicity")) {
          $query = "ALTER TABLE `$table`
				ADD UNIQUE INDEX `unicity` (`items_id`, `itemtype`, `isfather`)";
          $DB->query($query) or die($DB->error());
      }

       return true;
   }

   public static function uninstall() {
       $query = "DROP TABLE IF EXISTS `" . getTableForItemType(__CLASS__) . "`";
       return $GLOBALS['DB']->query($query) or die($GLOBALS['DB']->error());
   }

   public static function fatherYesNo($options = []) {
       $father_id = self::getFatherFromDB($_REQUEST['id'], $_REQUEST['itemtype']);
      if ($father_id) {
          Dropdown::showYesNo("father", $father_id['isfather']);
      } else {
          Dropdown::showYesNo("father");
      }
   }

   public static function getFatherFromDB($item_id, $itemtype) {
       global $DB;
       $req = $DB->request([
           'SELECT' => '*',
           'FROM'  => 'glpi_plugin_father_fathers',
           'WHERE' => ['items_id'=>$item_id, 'itemtype'=> strtolower($itemtype)]
       ]);
      foreach ($req as $row) {
          return $row;
      }

       return false;
   }

   public static function isFather($item_id, $itemtype) {
       $fatherDB = self::getFatherFromDB($item_id, $itemtype);
       return $fatherDB['isfather'];
   }

   public static function beforeUpdate($item) {
       $father_ticket = new self();
      if (isset($item->input['plugin_father_father_id'])) {
         if ($father_id = self::getFatherFromDB($item->fields['id'], get_class($item))) {
            $father_ticket->update(
                [
                'id' => $father_id['id'],
                'isfather' => $item->input['father']]
            );
         } else {
             $father_ticket->add([
                 'items_id' => $item->input['plugin_father_father_id'],
                 'isfather' => $item->input['father'],
                 'itemtype' => strtolower(get_class($item))
                 ]);
         }
      }
      if (isset($item->input['father'])) {
          $isfather = $item->input['father'];
      } else {
          $isfather = self::isFather($item->fields['id'], get_class($item));
      }
       $config = new PluginFatherConfig();

      if ((isset($item->input['status'])) && $isfather && !isset($item->input['_massive_father'])) {
          $son_ticket = new Ticket();
          $test_ticket = new Ticket();

         if ((isset($item->input['status']) && $config->isStatusImpacted($item->input['status']))) {
            foreach (Ticket_Ticket::getLinkedTicketsTo($item->fields['id']) as $tick) {
               $test_ticket->getFromDB($tick['tickets_id']);
               if ((isset($item->input['status']) && Ticket::isAllowedStatus($test_ticket->fields['status'], $item->input['status'])) || (isset($item->input['solutiontypes_id']) && Ticket::isAllowedStatus($test_ticket->fields['status'], 5))) {
                  if (isset($item->input['status']) && $item->input['status'] != $item->fields['status'] && !in_array($item->input['status'], [5,6])) {
                     $son_update = [
                      'id' => $tick['tickets_id'],
                      'status' => $item->input['status'],
                      '_auto_update' => true,
                      '_massive_father' => true
                     ];
                  } else {
                     if ($config->isSolutionOk() && $item->input['status'] != $item->fields['status'] && in_array($item->input['status'], [5,6]) && isset($_POST['solutiontypes_id']) && isset($_POST['itemtype']) && $_POST['itemtype']=="Ticket" && $_POST['items_id'] == $item->fields['id']) {
                            // copy solution
                            $em = new ITILSolution();
                            $inputSolution = [
                                'itemtype' => $test_ticket::getType(),
                                'items_id' => $tick['tickets_id'],
                                'content' => $_POST['content'],
                                'solutiontemplates_id' => $_POST['solutiontemplates_id'],
                            ];
                            $em->add($inputSolution);
                            //                                $son_update = [
                            //                                    'id' => $tick['tickets_id'],
                            //                                    //'status'       => $item->input['status'],
                            //                                    'solution' => $item->input['solution'],
                            //                                    'solutiontypes_id' => $item->input['solutiontypes_id'],
                            //                                    '_auto_update' => true,
                            //                                    '_massive_father' => true
                            //                                ];
                     } else {
                         $son_update = [
                             'id' => $tick['tickets_id'],
                             'status' => $item->input['status'],
                             '_auto_update' => true,
                             '_massive_father' => true];
                     }
                  }
                  if (isset($son_update)) {
                      $son_ticket->update($son_update);
                  }
               }
            }
         }
      }
   }
}
