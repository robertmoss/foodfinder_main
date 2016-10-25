<?php /* include this panel to have an in-page (vs modal) working display
    Three modes:
        Working: shows working message and graphic while job is executing
        Success: shows results of successful operation
        Error: shows results of warning operation
        Include workingPanel.js along with this partial to use
        
         set $showWorkingPanel = true before have the working panel not hidden upon initial load

*/ 
        $hidden=" hidden";
        if (isset($showWorkingPanel) && $showWorkingPanel) {
               $hidden="";
        }

        ?>
        
        <div id="workingPanel" class="alert alert-dismissable alert-info<?php echo $hidden;?>">
            <button type="button" class="close" aria-label="Close" onclick="hideElement('workingPanel');return(false);">
                <span aria-hidden="true">&times;</span>
            </button>
            <div id="workingPanelMessage">Working . . .</div>
            <div id="workingPanelIcon">
                <img src="<?php echo Config::$site_root?>/img/icons/ajax-loader.gif" />
            </div>
        </div>
