            <div id="modalIssueLog" class="modal fade" role="dialog">
                <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 id="modalDialogHeader" class="modal-title">Log an Issue</h4>
                  </div>
                  <div id="modalDialogBody" class="modal-body">
                    <form id="issueForm" action="service/issue.php" method="post">
                        <input id="txtIssueId" name="title" type="hidden" value="">
                        <div class="form-group">
                            <label for="txtIssueTitle">Title</label>
                            <input id="txtIssueTitle" name="title" type="text" class="form-control" placeholder="Title">
                        </div>
                        <div class="form-group">
                            <label for="txtIssueDescription">Description</label>
                            <textarea id="txtIssueDescription" name="body" class="form-control" placeholder="Description of issue" rows="5"></textarea>
                        </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                      <div id="modalIssueMessage" class="alert alert-success hidden" role="alert">Message goes here</div>
                        <button id="modalIssueCancel" type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</button>
                        <button id="modalIssueSubmit" type=" button" class="btn btn-primary" onclick="submitIssue();">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Log Issue
                        </button>
                  </div>
                </div>
              </div>
            </div>