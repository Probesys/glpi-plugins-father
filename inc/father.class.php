<?php
class PluginFatherFather extends  CommonDBTM {

   // From CommonDBTM
   public $dohistory = true;


   const TAG_SEARCH_NUM = 38500;


   public function showForm($ID, $options = array()) {
   
   }

   public static function install(Migration $migration) {
      global $DB;

      $table = getTableForItemType(__CLASS__);

      if (!TableExists($table)) {
         $query = "CREATE TABLE IF NOT EXISTS `$table` (
            	`id` INT(11) NOT NULL AUTO_INCREMENT,
            	`isfather` BOOLEAN NOT NULL DEFAULT '0',
            	`items_id` INT(11) NOT NULL DEFAULT '1',
            	`itemtype` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
            	PRIMARY KEY (`id`),
            	UNIQUE INDEX `unicity` (`itemtype`, `items_id`, `isfather`)
            )
            COLLATE='utf8_unicode_ci'
            ENGINE=MyISAM";
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



   static function fatherYesNo($options = array()) {
	   if ($father_id = self::getFatherFromDB($_REQUEST['id'],$_REQUEST['itemtype'])) {
		Dropdown::showYesNo("father",$father_id['isfather']);
      }
      else
      {
	      Dropdown::showYesNo("father"); 
      }
   }


   static function getFatherFromDB($item_id,$itemtype)
   {
	   if ($data_father = getAllDatasFromTable("glpi_plugin_father_fathers", '`items_id` = ' . $item_id.' and itemtype="'.strtolower($itemtype).'"')){
		   return reset($data_father);
	   
	   }

         return False;

   }


   static function isFather($item_id,$itemtype){
 	
 	$fatherDB=self::getFatherFromDB($item_id,$itemtype);
	return $fatherDB['isfather'];
   }

   static function beforeUpdate($item)
   {
    $father_ticket = new self(); 
    if (isset($item->input['plugin_father_father_id'])) {
    if ( $father_id = self::getFatherFromDB($item->fields['id'],get_class($item))) {
	     $father_ticket->update(array('id' => $father_id['id'],
		'isfather' => $item->input['father']));

    } else {

    $father_ticket->add(array('items_id' => $item->input['plugin_father_father_id'], 'isfather' => $item->input['father'], 'itemtype' => strtolower(get_class($item))));
        
    }                    
    }
   /* update of the child ticket 
    */
    if ( isset($item->input['father'])){
	$isfather=$item->input['father'];
    	}
    else {
	$isfather=self::isFather($item->fields['id'],get_class($item));
    	}

    if ($isfather && $item->input['status'] != $item->fields['status'] && !isset($item->input['_massive_father'])) { 
	    //print_r(Ticket_Ticket::getLinkedTicketsTo($item->fields['id']));
	    //exit;
            $son_ticket = new Ticket();
	    foreach (Ticket_Ticket::getLinkedTicketsTo($item->fields['id']) as $tick) {
               $son_ticket->update(array('id'           => $tick['tickets_id'],
                                     'status'       => $item->input['status'],
                                     '_auto_update' => true,
                                     '_massive_father' => true
                   ));
            }
    
 	  }
   }


}
