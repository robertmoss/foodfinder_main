<?php if ($user->hasRole('admin',$tenantID)) {
?>
<div id="floatingButtons" class="floatingControl hidden">
    <a id="editButton" class="btn btn-default btn-sm" href="#" role="button" ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
</div>
<div id="tenantContentEditModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="tenantContentHeader" class="modal-title">Edit Content</h4>
              </div>
              <div id="tenantContentFormAnchor" class="modal-body">Loading form . . .</div>
              <div class="modal-footer">
                    <div id="tenantContent-message" class="alert alert-danger hidden">
                        <a class="close_link" href="#" onclick="hideElement('tenantContent-message');"></a>
                        <span id='tenantContent-message_text'>Message goes here.</span>
                    </div>
                    <button id="btnTenantContentCancel" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                   <button id="btnTenantContentSave" type="button" class="btn btn-default" onclick="saveTenantContent();">Save</button>
             </div>
        </div>
   </div>
</div>
<?php } ?>