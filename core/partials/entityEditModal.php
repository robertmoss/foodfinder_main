<?php

/* Entity List partial 
 * To use: before including this file, set the following variables:
 *      $entityType: set to the value of the entity to be edited
 *      $callback: name of javascript function to use as callback after entity is saved
 * 
 * NOTE: Any page that uses this partial needs the following javascript includes:
 *      <script type="text/javascript" src="<?php echo Config::$site_root?>/js/validator.js"></script>
 *      <script type="text/javascript" src="<?php echo Config::$site_root?>/js/bootpag.min.js"></script>
 *      <script type="text/javascript" src="<?php echo Config::$site_root?>/js/jquery.form.min.js"></script>
 * 
 */
 
    if (!isset($callback)) {
        $callback = '';
        }
    if (!isset($modalSize)) {
        $modalSize="";
        }
 
 ?>
         <!-- Entity Edit Modal -->
         <div id="<?php echo $entityType;?>EditModal" class="modal fade" role="dialog">
            <div class="modal-dialog <?php echo $modalSize;?>">
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
                        <?php
                            $saveOnClick = "saveEntity('" . $entityType . "'";
                            if ($callback && strlen($callback)>0) {
                                $saveOnClick .= "," . $callback . "";
                            }
                            $saveOnClick .=  ")";
                        ?>
                        <button id="<?php echo $entityType;?>Save" type="button" class="btn btn-default" onclick="<?php echo $saveOnClick ?>">Save</button>
                    </div>
                </div>
            </div>
         </div>
 
 