			<div id="configModal" class="modal fade" role="dialog">
			  <div class="modal-dialog">
			    <!-- Modal content-->
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			        <h4 id="configHeader" class="modal-title">Settings</h4>
			      </div>
			      <div id="configBody" class="modal-body">
			      	<form id="settingsForm">
			      		<div class="panel panel-default">
					      	<div class="panel-heading">Show locations in the following categories:</div>
					      	<div id="categoryList" class="panel-body">
					        <?php
					        	/* to do: add logic to remember users settings across page loads */
					        	foreach(Utility::getList('categories',$tenantID,$userID) as $category) {
					        		echo '<div class="checkbox">';
					        		echo '	<label><input type="checkbox" class="categoryInput" value="' . $category['id'] . '" name="' . $category['name'] . '" > ' . $category['name'] . '</label>';
									echo '</div>';
					        	}
				        	?>
							</div>
				        </div>
				        <div class="form-group">
				            <label for="numToDisplay"># of Locations to Return</label>
				            <input id="numToDisplay" type="text" class="form-control" value="10"/>
				        </div>
				     </form>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</button>
			        <button type="button" class="btn btn-success" onclick="updateSettings();"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> OK</button>
			      </div>
			    </div>
			
			  </div>
			</div>