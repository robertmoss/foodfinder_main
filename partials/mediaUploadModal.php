			<div id="mediaUploadModal" class="modal fade" role="dialog">
			  	<div class="modal-dialog modal-lg">
			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 id="mediaUploadHeader" class="modal-title">Upload Media Items</h4>
			      </div>
			      <div id="mediaUploadBody" class="modal-body mediaModal">
			          <?php
			             $serviceURL = Config::getServiceRoot() . '/files.php'; 
                      ?>
                       <form id="uploadMediaForm" action="<?php echo $serviceURL?>" method="post" enctype="multipart/form-data">
                           <input name="importFile[]" type="file" multiple/>
                       </form>
			      </div>
			      <div class="modal-footer">
			          <div id="media-upload-message" class="alert alert-danger hidden">
                            <a class="close_link" href="#" onclick="hideElement('media-upload-message');"></a>
                            <span id='media-upload-text'>Message goes here.</span>
                        </div>
                        <div class="buttons">
	                       <button id="btnClose" type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</button>
                           <button id="mediaUpload" type="button" class="btn btn-primary" onclick="uploadMedia();"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload</button> 
                        </div>
			      </div>
			    </div>
			  </div>
			</div>