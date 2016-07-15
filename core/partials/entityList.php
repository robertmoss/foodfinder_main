<?php 

/* Entity List partial 
 * To use: before including this file, set the following variables:
 *      $entityType: set to the value of the entity to be included in the list
 *      $setName: what to call the set of entities (e.g. the plural version of the entity name)
 * 
 * NOTE: Any page that uses this partial needs the following javascript includes:
 *      <script type="text/javascript" src="<?php echo Config::$site_root?>/js/validator.js"></script>
 *      <script type="text/javascript" src="<?php echo Config::$site_root?>/js/bootpag.min.js"></script>
 *      <script type="text/javascript" src="<?php echo Config::$site_root?>/js/jquery.form.min.js"></script>
 * 
 */
?>
         <h1><?php echo ucfirst($entityType) ?></h1>
         <div id="feature-buttons" class="btn-group btn-default">
            <button class="btn btn-default" id="add<?php echo ucfirst($entityType);?>" onclick="editEntity(0,'<?php echo $entityType;?>');"><span class="glyphicon glyphicon-plus"></span> Add <?php echo ucfirst($entityType);?></button>
         </div>
         <div id="alertZone<?php echo ucfirst($entityType);?>"></div>
         <!-- Modal -->
         <div id="<?php echo $entityType;?>EditModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 id="<?php echo $entityType;?>Header" class="modal-title">Edit Entity</h4>
                    </div>
                    <div id="<?php echo $entityType;?>FormAnchor"></div>
                    <div class="modal-footer">
                        <div id="<?php echo $entityType;?>-message" class="alert alert-danger hidden">
                            <a class="close_link" href="#" onclick="hideElement('<?php echo $entityType;?>-message');"></a>
                            <span id='<?php echo $entityType;?>-message_text'>Message goes here.</span>
                        </div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-default" onclick="resetForm();">Reset</button>
                        <button id="<?php echo $entityType;?>Save" type="button" class="btn btn-default" onclick="saveEntity('<?php echo $entityType;?>');">Save</button>
                    </div>
                </div>
            </div>
         </div>
         <div id="<?php echo $entityType;?>List">
            <span id="<?php echo $entityType;?>ResultSpan">Loading <?php echo $entityType;?> . . .</span>
         </div>
         <div id="page-selection<?php echo ucfirst($entityType);?>"></div>
         <script type="text/javascript">loadEntityList('<?php echo $entityType;?>','<?php echo $setName;?>','Actions,Name,Author',10,0);</script>
                        