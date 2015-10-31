			<div id="locationModal" class="modal fade" role="dialog">
			  	<div class="modal-dialog">
			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 id="locationHeader" class="modal-title">Modal Header</h4>
			      </div>
			      <div id="locationBody" class="modal-body locationModal">
			        <p>Some text in the modal.</p>
			      </div>
			      <div class="modal-footer">
			      	<form class="form-inline">
			      		<?php if($userID>0) {?>
				      	<div class="checkbox align-left"><label><input id="chkVisited" type="checkbox" value="" onclick="setVisited();"> I've visited this place</label></div>
				      	<?php } ?>
				      	<?php if(Utility::userAllowed($user, 'location', 'edit', $tenantID)) {?>
				      	<button type="button" class="btn btn-default" onclick="editLocation();">
				      		<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
				      		</button>
				      	 <?php } ?>
				        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Close</button>
			      	</form>
			      </div>
			    </div>
			  </div>
			</div>