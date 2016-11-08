			<div id="mediaModal" class="modal fade" role="dialog">
			  	<div class="modal-dialog modal-lg">
			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 id="mediaHeader" class="modal-title">Media Item</h4>
			      </div>
			      <div id="mediaBody" class="modal-body mediaModal">
			        <div class="mediaImagePanel">
			            <img id="mediaImage" src=""/>
			        </div>
			        <div id="mediaFormAnchor">
			        </div>
			      </div>
			      <div class="modal-footer">
			          <?php include(Config::$core_path . "/partials/workingPanel.php"); ?>
			          <div id="media-message" class="alert alert-danger hidden">
                            <a class="close_link" href="#" onclick="hideElement('media-message');"></a>
                            <span id='media-message-text'>Message goes here.</span>
                        </div>
                        <div class="buttons">
                           <button id="btnDelete" type="button" class="btn btn-danger" onclick="deleteMedia();"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete Media</button>
	                       <button id="btnClose" type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</button>
                           <button id="mediaSave" type="button" class="btn btn-primary" onclick="saveMedia();"><span class="glyphicon glyphicon-save" aria-hidden="true"></span> Save</button> 
                        </div>
			      </div>
			    </div>
			  </div>
			</div>