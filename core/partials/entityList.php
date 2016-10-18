<?php 

/* Entity List partial 
 * To use: before including this file, set the following variables:
 *      $entityType: set to the value of the entity to be included in the list
 *      $setName: what to call the set of entities (e.g. the plural version of the entity name)
 *      $columns (optional): comman separated list of columns to include in list (besides name, which gets included automatically)
 * 
 * NOTE: Any page that uses this partial needs the following javascript includes:
 *      <script type="text/javascript" src="<?php echo Config::$site_root?>/js/validator.js"></script>
 *      <script type="text/javascript" src="<?php echo Config::$site_root?>/js/bootpag.min.js"></script>
 *      <script type="text/javascript" src="<?php echo Config::$site_root?>/js/jquery.form.min.js"></script>
 * 
 */
?>
         <?php
            if (!isset($friendlyName)) {
                $friendlyName=$entityType;
            }
            $columnList = 'Actions,Id,Name';
            if (isset($columns)&&strlen($columns)>0) {
                $columnList .= $columns;
            }
         ?>
         <h1><?php echo ucfirst($friendlyName) . 's' ?></h1>
         <div id="feature-buttons" class="btn-group btn-default">
            <button class="btn btn-default" id="add<?php echo ucfirst($entityType);?>" onclick="editEntity(0,'<?php echo $entityType;?>','after<?php echo ucfirst($entityType);?>FormLoad');"><span class="glyphicon glyphicon-plus"></span> Add <?php echo ucfirst($friendlyName);?></button>
         </div>
         <div id="alertZone<?php echo ucfirst($entityType);?>"></div>
         <?php include Config::$core_path . '/partials/entityEditModal.php'; ?>
         <div id="<?php echo $entityType;?>List">
            <span id="<?php echo $entityType;?>ResultSpan">Loading <?php echo $entityType;?> . . .</span>
         </div>
         <div id="page-selection<?php echo ucfirst($entityType);?>"></div>
         <script type="text/javascript">function load<?php echo ucfirst($entityType)?>List() {
            loadEntityList('<?php echo $entityType;?>','<?php echo $setName;?>','<?php echo $columnList;?>',10,0);
            }
         </script>
                        