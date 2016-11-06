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
                    <?php if($userID>0) {?>
                        <div class="btn-group" role="group" aria-label="...">
                             <button id="btnViewMaster" type="button" class="btn btn-default" onclick="window.location.href='core/entityPage.php?type=location&id='+    document.getElementById('locationid').innerText + '&mode=view';">
                                View Master Record
                             </button>
                             <button id="btnEditLocation" type="button" class="btn btn-default" onclick="editLocation();">
                                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                             </button>
                         </div>
                         <?php } ?>
			      	<form class="form-inline">
			      		<div>
    			      		<?php if($userID>0) {?>
                            <div class="checkbox align-left"><label><input id="chkVisited" type="checkbox" value="" onclick="setVisited();"> I've been there</label></div>
			     	      	<?php } ?>
			     	      	<div class="buttons">
	                           <button id="btnClose" type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Close</button>
                               <a href="#" target="_blank" id="btnDirections" type="button" class="btn btn-default hidden" ><span class="glyphicon glyphicon-road" aria-hidden="true"></span> Directions</a>
                            </div>
				        </div>
			      	</form>
			      </div>
			    </div>
			  </div>
			</div>