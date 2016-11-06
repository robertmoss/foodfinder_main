<?php $twitterHandle = Utility::getTenantPropertyEx($applicationID, $tenantID, $userID, 'twitterHandle', '');
                                if (strlen($twitterHandle)>0) { ?>                               
                                <a class="social icon twitter-follow-button" href="https://twitter.com/<?php echo $twitterHandle?>" data-size="large" data-show-count="false">Follow @<?php echo $twitterHandle?></a>
                                <?php } ?>